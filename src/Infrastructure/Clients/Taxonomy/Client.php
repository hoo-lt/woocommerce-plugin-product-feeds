<?php

namespace Hoo\ProductFeeds\Infrastructure\Clients\Taxonomy;

use Hoo\ProductFeeds\Domain;

use wpdb;

class Client
{
	protected const KEY = 'product_feeds';

	protected wpdb $wpdb;

	public function __construct()
	{
		global $wpdb;

		$this->wpdb = $wpdb;
	}

	public function getExcludedIds(): array
	{
		return $this->wpdb->get_col($this->wpdb->prepare(
			"WITH RECURSIVE excluded_tree AS (
        SELECT term_taxonomy.term_taxonomy_id, term_taxonomy.term_id
        FROM {$this->wpdb->term_taxonomy} term_taxonomy
        INNER JOIN {$this->wpdb->termmeta} termmeta ON term_taxonomy.term_id = termmeta.term_id
        WHERE termmeta.meta_key = %s AND termmeta.meta_value = %s

        UNION

        SELECT term_taxonomy.term_taxonomy_id, term_taxonomy.term_id
        FROM {$this->wpdb->term_taxonomy} term_taxonomy
        INNER JOIN excluded_tree ON term_taxonomy.parent = excluded_tree.term_id
      ) SELECT term_taxonomy_id FROM excluded_tree",
			'product_feeds',
			'exclude'
		));
	}
}