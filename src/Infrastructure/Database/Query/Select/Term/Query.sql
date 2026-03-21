WITH RECURSIVE cte_term_taxonomy AS (
	SELECT
		term_taxonomy.term_id,
		term_taxonomy.parent,

		terms.name,
		terms.slug AS path

	FROM :term_taxonomy AS term_taxonomy

	JOIN :terms AS terms
		ON terms.term_id = term_taxonomy.term_id

	WHERE term_taxonomy.parent = 0
		AND term_taxonomy.taxonomy = %s

	UNION ALL

	SELECT
		term_taxonomy.term_id,
		term_taxonomy.parent,

		terms.name,
		CONCAT(cte_term_taxonomy.path, '/', terms.slug) AS path

	FROM :term_taxonomy AS term_taxonomy

	JOIN :terms AS terms
		ON terms.term_id = term_taxonomy.term_id

	JOIN cte_term_taxonomy
		ON cte_term_taxonomy.term_id = term_taxonomy.parent
)

SELECT DISTINCT
	term_id AS id,
	parent AS parent_id,
	name,
	path

FROM cte_term_taxonomy