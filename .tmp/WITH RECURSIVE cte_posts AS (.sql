WITH RECURSIVE cte_posts AS (
	SELECT
		posts.ID,
		posts.post_title,
		posts.post_name

	FROM wp_posts AS posts

	STRAIGHT_JOIN wp_term_relationships AS term_relationships
		ON term_relationships.object_id = posts.ID
	STRAIGHT_JOIN wp_term_taxonomy AS term_taxonomy
		ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
		AND term_taxonomy.taxonomy = 'product_type'
	STRAIGHT_JOIN wp_terms AS terms
		ON terms.term_id = term_taxonomy.term_id
		AND terms.slug = 'simple'


	WHERE posts.post_type = 'product'
		AND posts.post_status = 'publish'
),

cte_terms AS (
	SELECT
		posts.ID,
		term_taxonomy.taxonomy,
		term_taxonomy.parent,

		terms.term_id,
		terms.name,

		CONCAT('/', terms.slug, '/') AS url_path

	FROM cte_posts AS posts

	STRAIGHT_JOIN wp_term_relationships AS term_relationships
		ON term_relationships.object_id = posts.ID
	STRAIGHT_JOIN wp_term_taxonomy AS term_taxonomy
		ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
	STRAIGHT_JOIN wp_terms AS terms
		ON terms.term_id = term_taxonomy.term_id

	WHERE term_taxonomy.taxonomy IN (
			'product_brand',
			'product_cat'
		)

	UNION ALL

	SELECT
		posts.ID,
		term_taxonomy.taxonomy,
		term_taxonomy.parent,

		terms.term_id,
		terms.name,

		CONCAT(cte_terms.url_path, terms.slug, '/') AS url_path

	FROM cte_posts AS posts

	STRAIGHT_JOIN wp_term_relationships AS term_relationships
		ON term_relationships.object_id = posts.ID
	STRAIGHT_JOIN wp_term_taxonomy AS term_taxonomy
		ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
	STRAIGHT_JOIN wp_terms AS terms
		ON terms.term_id = term_taxonomy.term_id

	JOIN cte_terms
		ON cte_terms.term_id = term_taxonomy.parent

	WHERE term_taxonomy.taxonomy IN (
			'product_brand',
			'product_cat'
		)
),

cte_woocommerce_attribute_taxonomies AS (
	SELECT
		woocommerce_attribute_taxonomies.attribute_id,
		CONCAT('pa_', woocommerce_attribute_taxonomies.attribute_name) AS attribute_name,
		woocommerce_attribute_taxonomies.attribute_label

	FROM wp_woocommerce_attribute_taxonomies AS woocommerce_attribute_taxonomies
),

cte_attribute AS (
	SELECT
		term_relationships.object_id,
		term_taxonomy.taxonomy,
		terms.term_id,
		terms.name,
		woocommerce_attribute_taxonomies.attribute_id,
		woocommerce_attribute_taxonomies.attribute_label

	FROM cte_posts AS posts

	STRAIGHT_JOIN wp_term_relationships AS term_relationships
		ON term_relationships.object_id = posts.ID
	STRAIGHT_JOIN wp_term_taxonomy AS term_taxonomy
		ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
	STRAIGHT_JOIN wp_terms AS terms
		ON terms.term_id = term_taxonomy.term_id
	STRAIGHT_JOIN cte_woocommerce_attribute_taxonomies AS woocommerce_attribute_taxonomies
		ON woocommerce_attribute_taxonomies.attribute_name = term_taxonomy.taxonomy
)

SELECT
	posts.ID AS id,
	posts.post_title AS name,
	posts.post_name AS slug,
	price.meta_value AS price,
	stock.meta_value AS stock,
	gtin.meta_value AS gtin,
	brand.term_id AS brand_id,
	brand.name AS brand_name,
	brand.url_path AS brand_url_path,
	category.term_id AS category_id,
	category.name AS category_name,
	category.url_path AS category_url_path,
	attribute.attribute_id AS attribute_id,
	attribute.attribute_label AS attribute_name,
	attribute.term_id AS term_id,
	attribute.name AS term_name

FROM cte_posts AS posts

JOIN wp_postmeta AS price
	ON price.post_id = posts.ID
	AND price.meta_key = '_price'

LEFT JOIN wp_postmeta AS stock
	ON stock.post_id = posts.ID
	AND stock.meta_key = '_stock'
LEFT JOIN wp_postmeta AS gtin
	ON gtin.post_id = posts.ID
	AND gtin.meta_key = '_global_unique_id'
LEFT JOIN cte_terms AS brand
	ON brand.ID = posts.ID
	AND brand.taxonomy = 'product_brand'
LEFT JOIN cte_terms AS category
	ON category.ID = posts.ID
	AND category.taxonomy = 'product_cat'
LEFT JOIN cte_attribute AS attribute
	ON attribute.object_id = posts.ID