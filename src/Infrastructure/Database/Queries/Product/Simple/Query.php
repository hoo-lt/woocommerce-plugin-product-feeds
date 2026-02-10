<?php

namespace Hoo\ProductFeeds\Infrastructure\Database\Queries\Product\Simple;

use Hoo\ProductFeeds\Infrastructure;

use wpdb;

class Query implements Infrastructure\Database\Queries\QueryInterface
{
	protected array $includedIds = [];
	protected array $excludedIds = [];

	public function __construct(
		protected readonly wpdb $wpdb,
	) {
	}

	public function included(int ...$ids): self
	{
		$clone = clone $this;
		$clone->includedIds = $ids;

		return $clone;
	}

	public function excluded(int ...$ids): self
	{
		$clone = clone $this;
		$clone->excludedIds = $ids;

		return $clone;
	}

	public function __invoke(): string
	{
		$where['posts.post_type'] = '= \'product\'';
		$where['posts.post_status'] = '= \'publish\'';

		if ($this->includedIds) {
			$where['posts.ID'] = 'IN (' . implode(',', array_map(fn() => '%d', $this->includedIds)) . ')';
		}

		if ($this->excludedIds) {
			$where['posts.ID'] = 'NOT IN (' . implode(',', array_map(fn() => '%d', $this->excludedIds)) . ')';
		}

		$where = 'WHERE ' . implode(' AND ', array_map(fn($key, $value) => "$key $value", array_keys($where), array_values($where)));

		$query = <<<SQL
			WITH cte_posts AS (
				SELECT
					posts.ID,
					posts.post_title

				FROM {$this->wpdb->posts} AS posts

				JOIN {$this->wpdb->term_relationships} AS term_relationships
					ON term_relationships.object_id = posts.ID
				JOIN {$this->wpdb->term_taxonomy} AS term_taxonomy
					ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
					AND term_taxonomy.taxonomy = 'product_type'
				JOIN {$this->wpdb->terms} AS terms
					ON terms.term_id = term_taxonomy.term_id
					AND terms.slug = 'simple'

				{$where}
			),

			cte_woocommerce_attribute_taxonomies AS (
				SELECT
					CONCAT('pa_', woocommerce_attribute_taxonomies.attribute_name) AS attribute_name,
					woocommerce_attribute_taxonomies.attribute_label

				FROM {$this->wpdb->prefix}woocommerce_attribute_taxonomies AS woocommerce_attribute_taxonomies
			),

			cte_terms AS (
				SELECT
					term_relationships.object_id,
					term_taxonomy.taxonomy,
					terms.name

				FROM cte_posts AS posts

				STRAIGHT_JOIN {$this->wpdb->term_relationships} AS term_relationships
					ON term_relationships.object_id = posts.ID
				STRAIGHT_JOIN {$this->wpdb->term_taxonomy} AS term_taxonomy
					ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
				STRAIGHT_JOIN {$this->wpdb->terms} AS terms
					ON terms.term_id = term_taxonomy.term_id

				WHERE term_taxonomy.taxonomy IN (
					'product_brand',
					'product_cat'
				)
			),

			cte_attribute AS (
				SELECT
					term_relationships.object_id,
					term_taxonomy.taxonomy,
					terms.name,
					woocommerce_attribute_taxonomies.attribute_label

				FROM cte_posts AS posts

				STRAIGHT_JOIN {$this->wpdb->term_relationships} AS term_relationships
					ON term_relationships.object_id = posts.ID
				STRAIGHT_JOIN {$this->wpdb->term_taxonomy} AS term_taxonomy
					ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
				STRAIGHT_JOIN {$this->wpdb->terms} AS terms
					ON terms.term_id = term_taxonomy.term_id
				STRAIGHT_JOIN cte_woocommerce_attribute_taxonomies AS woocommerce_attribute_taxonomies
					ON woocommerce_attribute_taxonomies.attribute_name = term_taxonomy.taxonomy
			)

			SELECT
				posts.ID AS id,
				posts.post_title AS name,
				price.meta_value AS price,
				stock.meta_value AS stock,
				ean.meta_value AS ean,
				brand.name AS brand_name,
				category.name AS category_name,
				attribute.attribute_label AS attribute_name,
				attribute.name AS term_name

			FROM cte_posts AS posts

			JOIN {$this->wpdb->postmeta} AS price
				ON price.post_id = posts.ID
				AND price.meta_key = '_price'

			LEFT JOIN {$this->wpdb->postmeta} AS stock
				ON stock.post_id = posts.ID
				AND stock.meta_key = '_stock'
			LEFT JOIN {$this->wpdb->postmeta} AS ean
				ON ean.post_id = posts.ID
				AND ean.meta_key = '_global_unique_id'
			LEFT JOIN cte_terms AS brand
				ON brand.object_id = posts.ID
				AND brand.taxonomy = 'product_brand'
			LEFT JOIN cte_terms AS category
				ON category.object_id = posts.ID
				AND category.taxonomy = 'product_cat'
			LEFT JOIN cte_attribute AS attribute
				ON attribute.object_id = posts.ID
		SQL;

		return $this->wpdb->prepare($query, ...[
			...$this->includedIds,
			...$this->excludedIds,
		]);
	}
}