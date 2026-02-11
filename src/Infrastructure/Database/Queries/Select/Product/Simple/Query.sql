WITH cte_term_relationships AS (
	SELECT DISTINCT
		term_relationships.object_id

	FROM :term_relationships AS term_relationships

	:WHERE
),

cte_posts AS (
	SELECT
		posts.ID,
		posts.post_title,
		posts.post_name

	FROM :posts AS posts

	JOIN :term_relationships AS term_relationships
		ON term_relationships.object_id = posts.ID
	JOIN :term_taxonomy AS term_taxonomy
		ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
		AND term_taxonomy.taxonomy = 'product_type'
	JOIN :terms AS terms
		ON terms.term_id = term_taxonomy.term_id
		AND terms.slug = 'simple'

	LEFT JOIN cte_term_relationships
		ON cte_term_relationships.object_id = posts.ID

	WHERE posts.post_type = 'product'
		AND posts.post_status = 'publish'
		AND cte_term_relationships.object_id IS NULL
),

cte_woocommerce_attribute_taxonomies AS (
	SELECT
		CONCAT('pa_', woocommerce_attribute_taxonomies.attribute_name) AS attribute_name,
		woocommerce_attribute_taxonomies.attribute_label

	FROM :woocommerce_attribute_taxonomies AS woocommerce_attribute_taxonomies
),

cte_terms AS (
	SELECT
		term_relationships.object_id,
		term_taxonomy.taxonomy,
		terms.term_id,
		terms.name,
		terms.slug

	FROM cte_posts AS posts

	STRAIGHT_JOIN :term_relationships AS term_relationships
		ON term_relationships.object_id = posts.ID
	STRAIGHT_JOIN :term_taxonomy AS term_taxonomy
		ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
	STRAIGHT_JOIN :terms AS terms
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
		terms.term_id,
		terms.name,
		terms.slug,
		woocommerce_attribute_taxonomies.attribute_label

	FROM cte_posts AS posts

	STRAIGHT_JOIN :term_relationships AS term_relationships
		ON term_relationships.object_id = posts.ID
	STRAIGHT_JOIN :term_taxonomy AS term_taxonomy
		ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
	STRAIGHT_JOIN :terms AS terms
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
	brand.slug AS brand_slug,
	category.term_id AS category_id,
	category.name AS category_name,
	category.slug AS category_slug,
	attribute.attribute_label AS attribute_name,
	attribute.name AS term_name

FROM cte_posts AS posts

JOIN :postmeta AS price
	ON price.post_id = posts.ID
	AND price.meta_key = '_price'

LEFT JOIN :postmeta AS stock
	ON stock.post_id = posts.ID
	AND stock.meta_key = '_stock'
LEFT JOIN :postmeta AS gtin
	ON gtin.post_id = posts.ID
	AND gtin.meta_key = '_global_unique_id'
LEFT JOIN cte_terms AS brand
	ON brand.object_id = posts.ID
	AND brand.taxonomy = 'product_brand'
LEFT JOIN cte_terms AS category
	ON category.object_id = posts.ID
	AND category.taxonomy = 'product_cat'
LEFT JOIN cte_attribute AS attribute
	ON attribute.object_id = posts.ID