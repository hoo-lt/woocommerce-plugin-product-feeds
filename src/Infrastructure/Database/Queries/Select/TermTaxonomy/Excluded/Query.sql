WITH RECURSIVE cte_term_taxonomy AS (
	SELECT
		term_taxonomy.term_taxonomy_id

	FROM :term_taxonomy AS term_taxonomy

	JOIN :termmeta AS termmeta
		ON termmeta.term_id = term_taxonomy.term_id

	WHERE termmeta.meta_key = %s
		AND termmeta.meta_value = %s

	UNION ALL

	SELECT
		term_taxonomy.term_taxonomy_id

	FROM :term_taxonomy AS term_taxonomy

	JOIN cte_term_taxonomy
		ON cte_term_taxonomy.term_taxonomy_id = term_taxonomy.parent
)

SELECT DISTINCT
	term_relationships.object_id

FROM :term_relationships AS term_relationships

WHERE term_relationships.term_taxonomy_id NOT IN (
	SELECT
		cte_term_taxonomy.term_taxonomy_id

	FROM cte_term_taxonomy
)