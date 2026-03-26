SELECT
	JSON_ARRAYAGG(
			JSON_OBJECT(
					'name', name,
					'slug', slug,
					'terms', terms
			)
	) AS attributes
FROM (
	SELECT
		woocommerce_attribute_taxonomies.attribute_label AS name,
		woocommerce_attribute_taxonomies.attribute_name AS slug,
		JSON_ARRAYAGG(
				JSON_OBJECT(
						'name', terms.name,
						'slug', terms.slug
				)
		) AS terms
	FROM :woocommerce_attribute_taxonomies AS woocommerce_attribute_taxonomies

	JOIN :term_taxonomy AS term_taxonomy
		ON term_taxonomy.taxonomy = CONCAT('pa_', woocommerce_attribute_taxonomies.attribute_name)
	JOIN :terms AS terms
		ON terms.term_id = term_taxonomy.term_id

	GROUP BY woocommerce_attribute_taxonomies.attribute_name
) AS json