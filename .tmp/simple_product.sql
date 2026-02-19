WITH RECURSIVE cte_term_taxonomy AS (
	SELECT
		term_taxonomy.term_taxonomy_id,
		term_taxonomy.term_id,
		term_taxonomy.taxonomy,
		term_taxonomy.parent,

		terms.name,

		CONCAT('/', terms.slug, '/') AS path

	FROM wp_term_taxonomy AS term_taxonomy

	JOIN wp_terms AS terms
		ON terms.term_id = term_taxonomy.term_id

	WHERE term_taxonomy.parent = 0
		AND term_taxonomy.taxonomy IN (
			'product_brand',
			'product_cat'
		)

	UNION

	SELECT
		term_taxonomy.term_taxonomy_id,
		term_taxonomy.term_id,
		term_taxonomy.taxonomy,
		term_taxonomy.parent,

		terms.name,

		CONCAT(cte_term_taxonomy.path, terms.slug, '/') AS path

	FROM wp_term_taxonomy AS term_taxonomy

	JOIN wp_terms AS terms
		ON terms.term_id = term_taxonomy.term_id

	JOIN cte_term_taxonomy
		ON cte_term_taxonomy.term_taxonomy_id = term_taxonomy.parent
),









term_relationships AS (
	SELECT DISTINCT object_id
	FROM wp_term_relationships
	WHERE term_taxonomy_id NOT IN (SELECT term_taxonomy_id FROM cte_term_taxonomy)
),

posts AS (
	SELECT
		wp_posts.ID,
		wp_posts.post_title
	FROM wp_posts
	JOIN wp_term_relationships ON wp_term_relationships.object_id = wp_posts.ID
	JOIN term_taxonomy ON term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
		AND term_taxonomy.taxonomy = 'product_type'
	JOIN terms ON terms.term_id = term_taxonomy.term_id
		AND terms.slug = 'simple'
	LEFT JOIN term_relationships ON term_relationships.object_id = wp_posts.ID
	WHERE wp_posts.post_type = 'product'
		AND wp_posts.post_status = 'publish'
		AND term_relationships.object_id IS NULL
),

terms_with_paths AS (
	SELECT
		tr.object_id,
		tth.taxonomy,
		tth.path,
		t.name
	FROM wp_term_relationships tr
	JOIN term_taxonomy_hierarchy tth ON tr.term_taxonomy_id = tth.term_taxonomy_id
    JOIN terms t ON tth.term_id = t.term_id
	WHERE tth.taxonomy IN ('product_brand', 'product_cat')
),

woocommerce_attribute_taxonomies AS (
  SELECT CONCAT('pa_', attribute_name) AS attribute_name, attribute_label
  FROM wp_woocommerce_attribute_taxonomies
),

attribute AS (
	SELECT
		wp_term_relationships.object_id,
		term_taxonomy.taxonomy,
		terms.name,
		wat.attribute_label
	FROM posts
	STRAIGHT_JOIN wp_term_relationships ON wp_term_relationships.object_id = posts.ID
	STRAIGHT_JOIN term_taxonomy ON term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
	STRAIGHT_JOIN terms ON terms.term_id = term_taxonomy.term_id
	STRAIGHT_JOIN woocommerce_attribute_taxonomies wat ON wat.attribute_name = term_taxonomy.taxonomy
)

SELECT
	p.ID AS id,
	p.post_title AS name,
	price.meta_value AS price,
	stock.meta_value AS stock,
	ean.meta_value AS ean,
	brand.name AS brand_name,
	brand.path AS brand_url_path,
	category.name AS category_name,
	category.path AS category_url_path,
	attr.attribute_label AS attribute_name,
	attr.name AS term_name
FROM posts p
JOIN wp_postmeta AS price ON price.post_id = p.ID AND price.meta_key = '_price'
LEFT JOIN wp_postmeta AS stock ON stock.post_id = p.ID AND stock.meta_key = '_stock'
LEFT JOIN wp_postmeta AS ean ON ean.post_id = p.ID AND ean.meta_key = '_global_unique_id'
LEFT JOIN terms_with_paths AS brand ON brand.object_id = p.ID AND brand.taxonomy = 'product_brand'
LEFT JOIN terms_with_paths AS category ON category.object_id = p.ID AND category.taxonomy = 'product_cat'
LEFT JOIN attribute AS attr ON attr.object_id = p.ID;