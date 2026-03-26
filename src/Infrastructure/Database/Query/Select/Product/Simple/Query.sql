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
		AND term_taxonomy.taxonomy = 'product_type'
	JOIN :terms AS terms
		ON terms.term_id = term_taxonomy.term_id
		AND terms.slug = 'simple'

	WHERE posts.post_type = 'product'
		:AND posts.ID
		:AND posts.post_status
),

cte_term_taxonomy AS (
	SELECT
		cte_posts.id AS post_id,
		term_taxonomy.taxonomy,
		JSON_ARRAYAGG(
			JSON_OBJECT('id', term_taxonomy.term_id)
		) AS terms

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
		cte_posts.id,
		term_taxonomy.taxonomy
),

cte_pa AS (
	SELECT
		post_id,
		JSON_ARRAYAGG(
			JSON_OBJECT(
				'slug', slug,
				'terms', terms
			)
		) AS attributes
	FROM (
		SELECT
			cte_posts.id AS post_id,
			TRIM(LEADING 'pa_' FROM term_taxonomy.taxonomy) AS slug,
			JSON_ARRAYAGG(
				JSON_OBJECT('slug', terms.slug)
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
			cte_posts.id,
			term_taxonomy.taxonomy
	) AS attributes

	GROUP BY
		post_id
)

SELECT
  JSON_ARRAYAGG(
    JSON_OBJECT(
      'id', id,
      'name', name,
      'path', path,
      'price', CAST(price AS DECIMAL(10,2)),
      'stock', CAST(stock AS SIGNED),
      'global_unique_id', global_unique_id,
      'product_attributes', product_attributes,
      'brands', brands,
      'categories', categories,
      'tags', tags,
      'attributes', attributes
    )
  ) AS products
FROM (
  SELECT
    cte_posts.id,
    cte_posts.name,
    cte_posts.slug AS path,
    price.meta_value AS price,
    stock.meta_value AS stock,
    global_unique_id.meta_value AS global_unique_id,
    product_attributes.meta_value AS product_attributes,
    brands.terms AS brands,
    categories.terms AS categories,
    tags.terms AS tags,
    cte_pa.attributes

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
    ON product_attributes.post_id = cte_posts.id
		AND product_attributes.meta_key = '_product_attributes'

  LEFT JOIN cte_term_taxonomy AS brands
    ON brands.post_id = cte_posts.id
		AND brands.taxonomy = 'product_brand'
  LEFT JOIN cte_term_taxonomy AS categories
    ON categories.post_id = cte_posts.id
		AND categories.taxonomy = 'product_cat'
  LEFT JOIN cte_term_taxonomy AS tags
    ON tags.post_id = cte_posts.id
		AND tags.taxonomy = 'product_tag'
  LEFT JOIN cte_pa
    ON cte_pa.post_id = cte_posts.id
) AS json;