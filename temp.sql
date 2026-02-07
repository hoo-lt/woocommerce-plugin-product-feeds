SELECT
    posts.ID,
    posts.post_type,
    posts.post_parent,
    posts.post_title,
    tt.taxonomy,
    terms.term_id,
    terms.name AS value_name
FROM wp_posts AS posts

-- 1. Джойним атрибуты (через родителя в любом случае, так как в Woo структура там)
INNER JOIN wp_term_relationships AS tr ON tr.object_id = (
    CASE WHEN posts.post_type = 'product_variation' THEN posts.post_parent ELSE posts.ID END
)
INNER JOIN wp_term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id AND tt.taxonomy LIKE 'pa_%'
INNER JOIN wp_terms AS terms ON tt.term_id = terms.term_id

-- 2. Цепляем мету конкретно этой строки
LEFT JOIN wp_postmeta AS pm ON posts.ID = pm.post_id
    AND pm.meta_key = CONCAT('attribute_', tt.taxonomy)

WHERE (posts.ID = 1983  OR posts.post_parent = 1983 )
  AND posts.post_status = 'publish'
  AND (
    -- ЛОГИКА ДЛЯ ВАРИАЦИЙ:
    (posts.post_type = 'product_variation' AND (
        -- А) Значение в мете совпадает со слагом термина (конкретный выбор: Синий)
        pm.meta_value = terms.slug
        OR
        -- Б) В вариации выбрано "Любое значение" (пустая строка в мете)
        pm.meta_value = ''
        OR
        -- В) Этого атрибута НЕТ в настройках вариаций (наследуемая "жопа")
        -- Проверяем, что для этой таксономии у этой вариации вообще нет записи в postmeta
        NOT EXISTS (
            SELECT 1 FROM wp_postmeta AS pm_check
            WHERE pm_check.post_id = posts.ID
              AND pm_check.meta_key = CONCAT('attribute_', tt.taxonomy)
        )
    ))
    OR
    -- ЛОГИКА ДЛЯ РОДИТЕЛЯ:
    -- Выводим только те атрибуты, которые НЕ стали вариациями (общие)
    -- Если хочешь видеть у родителя ВООБЩЕ ВСЁ (как справочник) - оставь просто posts.post_type = 'product'
    (posts.post_type = 'product' AND NOT EXISTS (SELECT 1 FROM wp_posts AS child WHERE child.post_parent = posts.ID))
)
ORDER BY posts.ID, tt.taxonomy;





















SELECT
    wp_posts.post_title AS 'Товар',
    (SELECT wp_terms.name FROM wp_terms
     JOIN wp_term_taxonomy ON wp_terms.term_id = wp_term_taxonomy.term_id
     JOIN wp_term_relationships ON wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
     WHERE wp_term_relationships.object_id = IF(wp_posts.post_type = 'product_variation', wp_posts.post_parent, wp_posts.ID)
     AND wp_term_taxonomy.taxonomy = 'product_cat' LIMIT 1) AS 'Категория',

    (SELECT wp_terms.name FROM wp_terms
     JOIN wp_term_taxonomy ON wp_terms.term_id = wp_term_taxonomy.term_id
     JOIN wp_term_relationships ON wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
     WHERE wp_term_relationships.object_id = IF(wp_posts.post_type = 'product_variation', wp_posts.post_parent, wp_posts.ID)
     AND wp_term_taxonomy.taxonomy = 'product_brand' LIMIT 1) AS 'Бренд',

    (SELECT wp_terms.name FROM wp_terms
     JOIN wp_term_taxonomy ON wp_terms.term_id = wp_term_taxonomy.term_id
     JOIN wp_term_relationships ON wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id
     WHERE wp_term_relationships.object_id = IF(wp_posts.post_type = 'product_variation', wp_posts.post_parent, wp_posts.ID)
     AND wp_term_taxonomy.taxonomy = 'product_tag' LIMIT 1) AS 'Тег'

FROM wp_posts
WHERE wp_posts.post_status = 'publish'
  AND (
    -- Берем вариации
    wp_posts.post_type = 'product_variation'
    OR
    -- Или простые товары, у которых нет вариаций (чтобы не дублировать)
    (wp_posts.post_type = 'product' AND NOT EXISTS (
        SELECT 1 FROM wp_posts AS children WHERE children.post_parent = wp_posts.ID AND children.post_type = 'product_variation'
    ))
  )
ORDER BY wp_posts.ID;