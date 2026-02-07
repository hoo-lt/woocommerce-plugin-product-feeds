<?php

namespace Hoo\ProductFeeds\Infrastructure\Products;

use wpdb;

class Client
{
	protected wpdb $wpdb;

	public function __construct()
	{
		global $wpdb;

		$this->wpdb = $wpdb;
	}

	public function get()
	{
		return $this->wpdb->get_results("
        SELECT ID, post_title, post_content, post_excerpt
        FROM {$this->wpdb->posts}
        WHERE post_type = 'product'
        AND post_status = 'publish'
    ", ARRAY_A);
	}
}