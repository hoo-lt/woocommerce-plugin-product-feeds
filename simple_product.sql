WITH RECURSIVE term_taxonomy AS (
	SELECT
		wp_term_taxonomy.term_taxonomy_id,
		wp_term_taxonomy.term_id

	FROM wp_term_taxonomy

	JOIN wp_termmeta
		ON wp_termmeta.term_id = wp_term_taxonomy.term_id

	WHERE wp_term_taxonomy.taxonomy IN (
			'product_brand',
			'product_cat',
			'product_tag'
		)
		AND wp_termmeta.meta_key = 'product_feeds'
		AND wp_termmeta.meta_value = 'exclude'

	UNION ALL

	SELECT
		wp_term_taxonomy.term_taxonomy_id,
		wp_term_taxonomy.term_id

	FROM wp_term_taxonomy

	JOIN term_taxonomy
		ON term_taxonomy.term_id = wp_term_taxonomy.parent

	WHERE wp_term_taxonomy.taxonomy IN (
		'product_brand',
		'product_cat',
		'product_tag'
	)
),

term_relationships AS (
	SELECT DISTINCT
		wp_term_relationships.object_id

	FROM wp_term_relationships

	WHERE wp_term_relationships.term_taxonomy_id IN (
		SELECT
			term_taxonomy_id

		FROM term_taxonomy
	)
),






posts AS (
	SELECT
		wp_posts.ID,
		wp_posts.post_title

	FROM wp_posts

	JOIN wp_term_relationships
		ON wp_term_relationships.object_id = wp_posts.ID
	JOIN wp_term_taxonomy
		ON wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
		AND wp_term_taxonomy.taxonomy = 'product_type'
	JOIN wp_terms
		ON wp_terms.term_id = wp_term_taxonomy.term_id
		AND wp_terms.slug = 'simple'

	LEFT JOIN term_relationships
		ON term_relationships.object_id = wp_posts.ID

	WHERE wp_posts.post_type = 'product'
		AND wp_posts.post_status = 'publish'
		AND term_relationships.object_id IS NULL
),

woocommerce_attribute_taxonomies AS (
  SELECT
    CONCAT('pa_', attribute_name) AS attribute_name,
    attribute_label

  FROM wp_woocommerce_attribute_taxonomies
),

terms AS (
	SELECT
		wp_term_relationships.object_id,
		wp_term_taxonomy.taxonomy,
		wp_terms.name

	FROM posts

	STRAIGHT_JOIN wp_term_relationships
		ON wp_term_relationships.object_id = posts.ID
	STRAIGHT_JOIN wp_term_taxonomy
		ON wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
	STRAIGHT_JOIN wp_terms
		ON wp_terms.term_id = wp_term_taxonomy.term_id

	WHERE wp_term_taxonomy.taxonomy IN (
		'product_brand',
		'product_cat'
	)
),

attribute AS (
	SELECT
		wp_term_relationships.object_id,
		wp_term_taxonomy.taxonomy,
		wp_terms.name,
		woocommerce_attribute_taxonomies.attribute_label

	FROM posts

	STRAIGHT_JOIN wp_term_relationships
		ON wp_term_relationships.object_id = posts.ID
	STRAIGHT_JOIN wp_term_taxonomy
		ON wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
	STRAIGHT_JOIN wp_terms
		ON wp_terms.term_id = wp_term_taxonomy.term_id
	STRAIGHT_JOIN woocommerce_attribute_taxonomies
		ON woocommerce_attribute_taxonomies.attribute_name = wp_term_taxonomy.taxonomy
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

FROM posts

JOIN wp_postmeta AS price
	ON price.post_id = posts.ID
	AND price.meta_key = '_price'

LEFT JOIN wp_postmeta AS stock
	ON stock.post_id = posts.ID
	AND stock.meta_key = '_stock'
LEFT JOIN wp_postmeta AS ean
	ON ean.post_id = posts.ID
	AND ean.meta_key = '_global_unique_id'
LEFT JOIN terms AS brand
	ON brand.object_id = posts.ID
	AND brand.taxonomy = 'product_brand'
LEFT JOIN terms AS category
	ON category.object_id = posts.ID
	AND category.taxonomy = 'product_cat'
LEFT JOIN attribute
	ON attribute.object_id = posts.ID