WITH posts AS (
	SELECT
		posts.ID AS id,
		posts.post_parent AS parent_id,
		parent_posts.post_title AS name,
		posts.post_content AS description,
		posts.post_excerpt AS short_description,
		parent_posts.post_excerpt AS parent_short_description,
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

parent_posts AS (
	SELECT DISTINCT
		parent_id AS id

	FROM posts
),

term_ids AS (
	SELECT
		parent_posts.id AS post_parent_id,
		term_taxonomy.taxonomy,
		COALESCE(
			JSON_ARRAYAGG(
				term_taxonomy.term_id
			),
			JSON_ARRAY()
		) AS term_ids

	FROM parent_posts

	JOIN :term_relationships AS term_relationships
		ON term_relationships.object_id = parent_posts.id
	JOIN :term_taxonomy AS term_taxonomy
		ON term_taxonomy.term_taxonomy_id = term_relationships.term_taxonomy_id

	WHERE term_taxonomy.taxonomy IN (
			'product_brand',
			'product_cat',
			'product_tag'
		)

	GROUP BY
		post_parent_id,
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
				LEADING 'attribute_pa_'

				FROM postmeta.meta_key
			) AS slug,
			COALESCE(
				JSON_ARRAYAGG(
					JSON_OBJECT('slug', postmeta.meta_value)
				),
				JSON_ARRAY()
			) AS terms

		FROM posts

		JOIN :postmeta AS postmeta
			ON postmeta.post_id = posts.id

		WHERE postmeta.meta_key LIKE 'attribute_pa_%'

		GROUP BY
			post_id,
			postmeta.meta_key
	) AS terms

	GROUP BY
		post_id
),

parent_attributes AS (
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
		) AS parent_attributes

	FROM (
		SELECT
			parent_posts.id AS post_id,
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

		FROM parent_posts

		JOIN :term_relationships AS term_relationships
			ON term_relationships.object_id = parent_posts.id
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
						WHEN meta_key = '_thumbnail_id'
						THEN meta_value
					END
				),
				''
			) AS UNSIGNED
		) AS image_id

	FROM :postmeta

	JOIN posts
		ON posts.id = post_id

	WHERE meta_key IN (
			'_regular_price',
			'_sale_price',
			'_sale_price_dates_from',
			'_sale_price_dates_to',
			'_global_unique_id',
			'_manage_stock',
			'_stock',
			'_stock_status',
			'_thumbnail_id'
		)

	GROUP BY
		post_id
),

parent_postmeta AS (
	SELECT
		parent_posts.id AS post_id,
		MAX(
			CASE
				WHEN meta_key = '_global_unique_id'
				THEN meta_value
			END
		) AS gtin,
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

	JOIN parent_posts
		ON parent_posts.id = post_id

	WHERE meta_key IN (
			'_global_unique_id',
			'_stock',
			'_stock_status',
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
				'parent_id', parent_id,
				'name', name,
				'description', description,
				'slug', slug,
				'gtin', gtin,
				'price', price,
				'stock', stock,
				'parent_stock', parent_stock,
				'image_ids', image_ids,
				'parent_product_attributes', parent_product_attributes,
				'brand_ids', brand_ids,
				'category_ids', category_ids,
				'tag_ids', tag_ids,
				'attributes', attributes,
				'parent_attributes', parent_attributes
			)
		),
		JSON_ARRAY()
	) AS products

FROM (
	SELECT
		posts.id,
		posts.parent_id,
		posts.name,
		COALESCE(
			NULLIF(posts.short_description, ''),
			NULLIF(posts.parent_short_description),
			NULLIF(posts.parent_description)
		) AS description,
		posts.slug,
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
		COALESCE(
			parent_attributes.parent_attributes,
			JSON_ARRAY()
		) AS parent_attributes,
		JSON_OBJECT(
			'regular', postmeta.regular_price,
			'sale', postmeta.sale_price,
			'sale_dates_from', postmeta.sale_price_dates_from,
			'sale_dates_to', postmeta.sale_price_dates_to
		) AS price,
		COALESCE(postmeta.gtin, parent_postmeta.gtin) AS gtin,
		JSON_OBJECT(
			'manage', postmeta.manage_stock,
			'status', postmeta.stock_status,
			'quantity', postmeta.stock_quantity
		) AS stock,
		JSON_OBJECT(
			'status', parent_postmeta.stock_status,
			'quantity', parent_postmeta.stock_quantity
		) AS parent_stock,
		JSON_OBJECT(
			'image_id', COALESCE(postmeta.image_id, parent_postmeta.image_id),
			'image_ids', parent_postmeta.image_ids
		) AS image_ids,
		parent_postmeta.product_attributes AS parent_product_attributes

	FROM posts

	LEFT JOIN term_ids AS brand_ids
		ON brand_ids.post_parent_id = posts.parent_id
		AND brand_ids.taxonomy = 'product_brand'
	LEFT JOIN term_ids AS category_ids
		ON category_ids.post_parent_id = posts.parent_id
		AND category_ids.taxonomy = 'product_cat'
	LEFT JOIN term_ids AS tag_ids
		ON tag_ids.post_parent_id = posts.parent_id
		AND tag_ids.taxonomy = 'product_tag'
	LEFT JOIN attributes
		ON attributes.post_id = posts.id
	LEFT JOIN parent_attributes
		ON parent_attributes.post_id = posts.parent_id
	LEFT JOIN postmeta
		ON postmeta.post_id = posts.id
	LEFT JOIN parent_postmeta
		ON parent_postmeta.post_id = posts.parent_id
) AS json