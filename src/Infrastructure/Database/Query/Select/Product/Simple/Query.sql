WITH cte_posts AS (
	SELECT
		posts.ID AS id,
		posts.post_title AS name,
		posts.post_name AS slug

	FROM :posts AS posts

	JOIN :term_relationships AS term_relationships
		ON term_relationships.object_id = posts.ID
	JOIN :term_taxonomy AS term_taxonomy
		ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
	JOIN :terms AS terms
		ON terms.term_id = term_taxonomy.term_id

	WHERE posts.post_type = 'product'
		AND term_taxonomy.taxonomy = 'product_type'
		AND terms.slug = 'simple'
		:AND posts.ID
		:AND posts.post_status
),

cte_term_ids AS (
	SELECT
		cte_posts.id AS post_id,
		term_taxonomy.taxonomy,
		COALESCE(
			JSON_ARRAYAGG(
				term_taxonomy.term_id
			),
			JSON_ARRAY()
		) AS term_ids

	FROM cte_posts

	JOIN :term_relationships AS term_relationships
		ON term_relationships.object_id = cte_posts.id
	JOIN :term_taxonomy AS term_taxonomy
		ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id

	WHERE term_taxonomy.taxonomy IN (
			'product_brand',
			'product_cat',
			'product_tag'
		)

	GROUP BY
		post_id,
		taxonomy
),

cte_taxonomy_attributes AS (
	SELECT
		post_id,
		COALESCE(
			JSON_ARRAYAGG(
				JSON_OBJECT(
					'slug', slug,
					'terms', terms
				)
			),
			JSON_ARRAY()
		) AS taxonomy_attributes
	FROM (
		SELECT
			cte_posts.id AS post_id,
			TRIM(LEADING 'pa_' FROM term_taxonomy.taxonomy) AS slug,
			COALESCE(
				JSON_ARRAYAGG(
					JSON_OBJECT('slug', terms.slug)
				),
				JSON_ARRAY()
			) AS terms

		FROM cte_posts

		JOIN :term_relationships AS term_relationships
			ON term_relationships.object_id = cte_posts.id
		JOIN :term_taxonomy AS term_taxonomy
			ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
		JOIN :terms AS terms
			ON terms.term_id = term_taxonomy.term_id

		WHERE term_taxonomy.taxonomy LIKE 'pa_%'

		GROUP BY
			post_id,
			taxonomy
	) AS taxonomy_attributes

	GROUP BY
		post_id
),

cte_postmeta AS (
	SELECT
		cte_posts.id AS post_id,
		CAST(MAX(CASE WHEN meta_key = '_regular_price' THEN meta_value END) AS DECIMAL(10,2)) AS regular_price,
		CAST(MAX(CASE WHEN meta_key = '_sale_price' THEN meta_value END) AS DECIMAL(10,2)) AS sale_price,
		CAST(NULLIF(MAX(CASE WHEN meta_key = '_sale_price_dates_from' THEN meta_value END), '0') AS UNSIGNED) AS sale_price_dates_from,
		CAST(NULLIF(MAX(CASE WHEN meta_key = '_sale_price_dates_to' THEN meta_value END), '0') AS UNSIGNED) AS sale_price_dates_to,
		CAST(MAX(CASE WHEN meta_key = '_stock' THEN meta_value END) AS SIGNED) AS stock,
		MAX(CASE WHEN meta_key = '_stock_status' THEN meta_value END) AS stock_status,
		MAX(CASE WHEN meta_key = '_global_unique_id' THEN meta_value END) AS global_unique_id,
		MAX(CASE WHEN meta_key = '_product_attributes' THEN meta_value END) AS product_attributes,
		CAST(NULLIF(MAX(CASE WHEN meta_key = '_thumbnail_id' THEN meta_value END), '') AS UNSIGNED) AS thumbnail_id,
		NULLIF(MAX(CASE WHEN meta_key = '_product_image_gallery' THEN meta_value END), '') AS image_gallery

	FROM :postmeta

	JOIN cte_posts
		ON cte_posts.id = post_id

	WHERE meta_key IN (
			'_regular_price',
			'_sale_price',
			'_sale_price_dates_from',
			'_sale_price_dates_to',
			'_stock',
			'_stock_status',
			'_global_unique_id',
			'_product_attributes',
			'_thumbnail_id',
			'_product_image_gallery'
		)

	GROUP BY
		post_id
)

SELECT
 COALESCE(JSON_ARRAYAGG(
 JSON_OBJECT(
 'id', id,
 'name', name,
 'path', path,
 'regular_price', regular_price,
 'sale_price', sale_price,
 'sale_price_dates_from', sale_price_dates_from,
 'sale_price_dates_to', sale_price_dates_to,
 'stock', stock,
 'stock_status', stock_status,
 'thumbnail_id', thumbnail_id,
 'image_gallery', image_gallery,
 'global_unique_id', global_unique_id,
 'product_attributes', product_attributes,
 'brand_ids', brand_ids,
 'category_ids', category_ids,
 'tag_ids', tag_ids,
 'taxonomy_attributes', taxonomy_attributes
 )
 ), JSON_ARRAY()) AS products
FROM (
 SELECT
 cte_posts.id,
 cte_posts.name,
 cte_posts.slug AS path,
 meta.regular_price,
 meta.sale_price,
 meta.sale_price_dates_from,
 meta.sale_price_dates_to,
 meta.stock,
 meta.stock_status,
 meta.thumbnail_id,
 meta.image_gallery,
 meta.global_unique_id,
 meta.product_attributes,
 COALESCE(brands.term_ids, JSON_ARRAY()) AS brand_ids,
 COALESCE(categories.term_ids, JSON_ARRAY()) AS category_ids,
 COALESCE(tags.term_ids, JSON_ARRAY()) AS tag_ids,
 COALESCE(cte_taxonomy_attributes.taxonomy_attributes, JSON_ARRAY()) AS taxonomy_attributes

 FROM cte_posts
 LEFT JOIN cte_postmeta AS meta
 ON meta.post_id = cte_posts.id

 LEFT JOIN cte_term_ids AS brands
 ON brands.post_id = cte_posts.id
 AND brands.taxonomy = 'product_brand'
 LEFT JOIN cte_term_ids AS categories
 ON categories.post_id = cte_posts.id
 AND categories.taxonomy = 'product_cat'
 LEFT JOIN cte_term_ids AS tags
 ON tags.post_id = cte_posts.id
 AND tags.taxonomy = 'product_tag'
 LEFT JOIN cte_taxonomy_attributes
 ON cte_taxonomy_attributes.post_id = cte_posts.id
) AS json;