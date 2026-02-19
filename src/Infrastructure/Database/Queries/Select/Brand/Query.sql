WITH RECURSIVE cte AS (
	SELECT
		term_taxonomy.term_taxonomy_id,
		term_taxonomy.parent,

		terms.name,

		terms.slug AS url_path

	FROM :term_taxonomy AS term_taxonomy

	JOIN :terms AS terms
		ON terms.term_id = term_taxonomy.term_id

	WHERE term_taxonomy.parent = 0
		AND term_taxonomy.taxonomy = 'product_brand'

	UNION

	SELECT
		term_taxonomy.term_taxonomy_id,
		term_taxonomy.parent,

		terms.name,

		CONCAT(cte.url_path, '/', terms.slug) AS url_path

	FROM :term_taxonomy AS term_taxonomy

	JOIN :terms AS terms
		ON terms.term_id = term_taxonomy.term_id

	JOIN cte
		ON cte.term_taxonomy_id = term_taxonomy.parent
)

SELECT
	term_taxonomy_id AS id,
	name,
	CONCAT(%s, '/', %s, '/', url_path, '/') AS url

FROM cte