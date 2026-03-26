WITH cte_posts AS (
	SELECT
		posts.ID AS id,
		posts.post_parent AS parent_id,
		parent_posts.post_title AS name,
		parent_posts.post_name AS slug

	FROM :posts AS posts

	JOIN :posts AS parent_posts
		ON parent_posts.ID = posts.post_parent

	WHERE posts.post_type = 'product_variation'
		:AND posts.ID
		:AND posts.post_status
		:AND parent_posts.ID
		:AND parent_posts.post_status
),

cte_term_taxonomy AS (
	SELECT
		cte_posts.id AS post_id,
		term_taxonomy.term_id,
		term_taxonomy.taxonomy

	FROM cte_posts

	JOIN :term_relationships AS term_relationships
		ON term_relationships.object_id = cte_posts.parent_id
	JOIN :term_taxonomy AS term_taxonomy
		ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
		AND term_taxonomy.taxonomy IN (
			'product_brand',
			'product_cat',
			'product_tag'
		)
),

cte_pa AS (
	SELECT
		cte_posts.id AS post_id,
		TRIM(LEADING 'pa_' FROM term_taxonomy.taxonomy) AS attribute_slug,
		terms.slug AS term_slug

	FROM cte_posts

	JOIN :term_relationships AS term_relationships
		ON term_relationships.object_id = cte_posts.parent_id
	JOIN :term_taxonomy AS term_taxonomy
		ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
		AND term_taxonomy.taxonomy LIKE 'pa_%'
	JOIN :terms AS terms
		ON terms.term_id = term_taxonomy.term_id
),

cte_attribute_pa AS (
	SELECT
		cte_posts.id AS post_id,
		TRIM(LEADING 'attribute_pa_' FROM postmeta.meta_key) AS attribute_slug,
		postmeta.meta_value AS term_slug

	FROM :postmeta AS postmeta

	JOIN cte_posts
		ON cte_posts.id = postmeta.post_id
		AND postmeta.meta_key LIKE 'attribute_pa_%'
)

SELECT
	cte_posts.id,
	cte_posts.parent_id,
	cte_posts.name,
	cte_posts.slug AS path,

	price.meta_value AS price,
	stock.meta_value AS stock,
	global_unique_id.meta_value AS global_unique_id,
	product_attributes.meta_value AS product_attributes,

	brand.term_id AS brand_id,
	category.term_id AS category_id,
	tag.term_id AS tag_id,

	COALESCE(cte_attribute_pa.attribute_slug, cte_pa.attribute_slug) AS attribute_slug,
	COALESCE(cte_attribute_pa.term_slug, cte_pa.term_slug) AS term_slug,

CASE
	WHEN cte_attribute_pa.term_slug IS NULL
		THEN FALSE
	WHEN cte_attribute_pa.term_slug = cte_pa.term_slug
		THEN TRUE
	ELSE NULL
END AS attribute_is_variable

FROM cte_posts

LEFT JOIN :postmeta AS price
	ON price.post_id = cte_posts.id
	AND price.meta_key = '_price'
LEFT JOIN :postmeta AS stock
	ON stock.post_id = cte_posts.id
	AND stock.meta_key = '_stock'
LEFT JOIN :postmeta AS global_unique_id
	ON global_unique_id.post_id = cte_posts.id
	AND global_unique_id.meta_key = '_global_unique_id'
LEFT JOIN :postmeta AS product_attributes
	ON product_attributes.post_id = cte_posts.parent_id
	AND product_attributes.meta_key = '_product_attributes'

LEFT JOIN cte_term_taxonomy AS brand
	ON brand.post_id = cte_posts.id
	AND brand.taxonomy = 'product_brand'
LEFT JOIN cte_term_taxonomy AS category
	ON category.post_id = cte_posts.id
	AND category.taxonomy = 'product_cat'
LEFT JOIN cte_term_taxonomy AS tag
	ON tag.post_id = cte_posts.id
	AND tag.taxonomy = 'product_tag'

LEFT JOIN cte_pa
	ON cte_pa.post_id = cte_posts.id
LEFT JOIN cte_attribute_pa
	ON cte_attribute_pa.post_id = cte_posts.id
	AND cte_attribute_pa.attribute_slug = cte_pa.attribute_slug

WHERE cte_attribute_pa.term_slug IS NULL
	OR cte_attribute_pa.term_slug = cte_pa.term_slug