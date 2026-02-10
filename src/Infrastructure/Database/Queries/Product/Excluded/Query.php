<?php

namespace Hoo\ProductFeeds\Infrastructure\Database\Queries\Product\Excluded;

use Hoo\ProductFeeds\Domain;
use Hoo\ProductFeeds\Infrastructure;

use wpdb;

class Query implements Infrastructure\Database\Queries\QueryInterface
{
	public function __construct(
		protected readonly wpdb $wpdb
	) {
	}

	public function __invoke(): string
	{
		$taxonomies = Domain\Taxonomy::cases();
		$term = Domain\Term::Exclude;

		$termTaxonomyTaxonomy = implode(', ', array_fill(0, count($taxonomies), '%s'));

		$sql = <<<SQL
			WITH RECURSIVE cte_term_taxonomy AS (
				SELECT
					term_taxonomy.term_taxonomy_id,
					term_taxonomy.term_id

				FROM {$this->wpdb->term_taxonomy} AS term_taxonomy

				JOIN {$this->wpdb->termmeta} AS termmeta
					ON termmeta.term_id = term_taxonomy.term_id

				WHERE term_taxonomy.taxonomy IN ({$termTaxonomyTaxonomy})
					AND termmeta.meta_key = 'product_feeds'
					AND termmeta.meta_value = %s

				UNION ALL

				SELECT
					term_taxonomy.term_taxonomy_id,
					term_taxonomy.term_id

				FROM {$this->wpdb->term_taxonomy} AS term_taxonomy

				JOIN cte_term_taxonomy
					ON cte_term_taxonomy.term_id = term_taxonomy.parent

				WHERE term_taxonomy.taxonomy IN ({$termTaxonomyTaxonomy})
			)

			SELECT DISTINCT
				term_relationships.object_id AS id

			FROM {$this->wpdb->term_relationships} AS term_relationships

			WHERE term_relationships.term_taxonomy_id IN (
				SELECT
					term_taxonomy_id

				FROM cte_term_taxonomy
			)
		SQL;

		$taxonomyValues = array_map(fn($taxonomy) => $taxonomy->value, $taxonomies);
		$termValue = $term->value;

		return $this->wpdb->prepare($sql, ...[
			...$taxonomyValues,
			$termValue,
			...$taxonomyValues
		]);
	}
}