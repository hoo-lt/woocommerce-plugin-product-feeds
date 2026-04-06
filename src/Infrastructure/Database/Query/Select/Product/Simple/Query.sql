WITH posts AS (
	SELECT
		posts.ID AS id,
		posts.post_title AS name,
		posts.post_content AS description,
		posts.post_excerpt AS short_description,
		posts.post_name AS slug,
		posts.post_status AS status

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

term_ids AS (
	SELECT
		posts.id AS post_id,
		term_taxonomy.taxonomy,
		COALESCE(
			JSON_ARRAYAGG(
				term_taxonomy.term_id
			),
			JSON_ARRAY()
		) AS term_ids

	FROM posts

	JOIN :term_relationships AS term_relationships
		ON term_relationships.object_id = posts.id
	JOIN :term_taxonomy AS term_taxonomy
		ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id

	WHERE term_taxonomy.taxonomy IN (
			'product_brand',
			'product_cat',
			'product_tag'
		)

	GROUP BY
		post_id,
		term_taxonomy.taxonomy
),

attributes AS (
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
		) AS attributes

	FROM (
		SELECT
			posts.id AS post_id,
			TRIM(
				LEADING 'pa_'

				FROM term_taxonomy.taxonomy
			) AS slug,
			COALESCE(
				JSON_ARRAYAGG(
					JSON_OBJECT('slug', terms.slug)
				),
				JSON_ARRAY()
			) AS terms

		FROM posts

		JOIN :term_relationships AS term_relationships
			ON term_relationships.object_id = posts.id
		JOIN :term_taxonomy AS term_taxonomy
			ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id
		JOIN :terms AS terms
			ON terms.term_id = term_taxonomy.term_id

		WHERE term_taxonomy.taxonomy LIKE 'pa_%'

		GROUP BY
			post_id,
			term_taxonomy.taxonomy
	) AS terms

	GROUP BY
		post_id
),

postmeta AS (
	SELECT
		posts.id AS post_id,
		CAST(
			NULLIF(
				MAX(
					CASE
						WHEN meta_key = '_regular_price'
						THEN meta_value
					END
				),
				''
			) AS DECIMAL(10,2)
		) AS regular_price,
		CAST(
			NULLIF(
				MAX(
					CASE
						WHEN meta_key = '_sale_price'
						THEN meta_value
					END
				),
				''
			) AS DECIMAL(10,2)
		) AS sale_price,
		CAST(
			NULLIF(
				MAX(
					CASE
						WHEN meta_key = '_sale_price_dates_from'
						THEN meta_value
					END
				),
				'0'
			) AS UNSIGNED
		) AS sale_price_dates_from,
		CAST(
			NULLIF(
				MAX(
					CASE
						WHEN meta_key = '_sale_price_dates_to'
						THEN meta_value
					END
				),
				'0'
			) AS UNSIGNED
		) AS sale_price_dates_to,
		MAX(
			CASE
				WHEN meta_key = '_sku'
				THEN meta_value
			END
		) AS sku,
		MAX(
			CASE
				WHEN meta_key = '_global_unique_id'
				THEN meta_value
			END
		) AS gtin,
		MAX(
			CASE
				WHEN meta_key = '_manage_stock'
				THEN meta_value
			END
		) AS manage_stock,
		MAX(
			CASE
				WHEN meta_key = '_stock_status'
				THEN meta_value
			END
		) AS stock_status,
		CAST(
			NULLIF(
				MAX(
					CASE
						WHEN meta_key = '_stock'
						THEN meta_value
					END
				),
				''
			) AS SIGNED
		) AS stock_quantity,
		CAST(
			NULLIF(
				MAX(
					CASE
						WHEN meta_key = '_thumbnail_id'
						THEN meta_value
					END
				),
				''
			) AS UNSIGNED
		) AS image_id,
		NULLIF(
			MAX(
				CASE
					WHEN meta_key = '_product_image_gallery'
					THEN meta_value
				END
			),
			''
		) AS image_ids,
		MAX(
			CASE
				WHEN meta_key = '_product_attributes'
				THEN meta_value
			END
		) AS product_attributes

	FROM :postmeta

	JOIN posts
		ON posts.id = post_id

	WHERE meta_key IN (
			'_regular_price',
			'_sale_price',
			'_sale_price_dates_from',
			'_sale_price_dates_to',
			'_sku',
			'_global_unique_id',
			'_manage_stock',
			'_stock_status',
			'_stock',
			'_thumbnail_id',
			'_product_image_gallery',
			'_product_attributes'
		)

	GROUP BY
		post_id
)

SELECT
	COALESCE(
		JSON_ARRAYAGG(
			JSON_OBJECT(
				'id', id,
				'name', name,
				'description', description,
				'slug', slug,
				'status', status,
				'price', price,
				'sku', sku,
				'gtin', gtin,
				'stock', stock,
				'image_ids', image_ids,
				'product_attributes', product_attributes,
				'brand_ids', brand_ids,
				'category_ids', category_ids,
				'tag_ids', tag_ids,
				'attributes', attributes
			)
		),
		JSON_ARRAY()
	) AS products

FROM (
	SELECT
		posts.id,
		posts.name,
		COALESCE(
			posts.short_description,
			posts.description
		) AS description,
		posts.slug,
		posts.status,
		COALESCE(
			brand_ids.term_ids,
			JSON_ARRAY()
		) AS brand_ids,
		COALESCE(
			category_ids.term_ids,
			JSON_ARRAY()
		) AS category_ids,
		COALESCE(
			tag_ids.term_ids,
			JSON_ARRAY()
		) AS tag_ids,
		COALESCE(
			attributes.attributes,
			JSON_ARRAY()
		) AS attributes,
		JSON_OBJECT(
			'regular', postmeta.regular_price,
			'sale', postmeta.sale_price,
			'sale_dates_from', postmeta.sale_price_dates_from,
			'sale_dates_to', postmeta.sale_price_dates_to
		) AS price,
		postmeta.sku,
		postmeta.gtin,
		JSON_OBJECT(
			'manage', postmeta.manage_stock,
			'status', postmeta.stock_status,
			'quantity', postmeta.stock_quantity
		) AS stock,
		JSON_OBJECT(
			'image_id', postmeta.image_id,
			'image_ids', postmeta.image_ids
		) AS image_ids,
		postmeta.product_attributes

	FROM posts

	LEFT JOIN term_ids AS brand_ids
		ON brand_ids.post_id = posts.id
		AND brand_ids.taxonomy = 'product_brand'
	LEFT JOIN term_ids AS category_ids
		ON category_ids.post_id = posts.id
		AND category_ids.taxonomy = 'product_cat'
	LEFT JOIN term_ids AS tag_ids
		ON tag_ids.post_id = posts.id
		AND tag_ids.taxonomy = 'product_tag'
	LEFT JOIN attributes
		ON attributes.post_id = posts.id
	LEFT JOIN postmeta
		ON postmeta.post_id = posts.id
) AS json