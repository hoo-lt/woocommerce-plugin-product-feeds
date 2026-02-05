<?php

namespace Hoo\ProductFeeds\Presentation\Controllers;

class Controller
{
	public function __construct(
		protected readonly string $id,
		protected readonly string $taxonomy,
	) {
		add_filter("manage_{$this->id}_columns", $this->add());
		add_filter("manage_{$this->taxonomy}_custom_column", $this->add2());
	}

	protected function add($columns)
	{
		$columns['product_feeds'] = __('Product feeds', 'woocommerce-plugin-product-feeds');
	}

	protected function add2(string $string, string $column_name, int $term_id)
	{
		switch ($column_name) {
			case 'product_feeds':
				echo get_term_meta($term_id, '_product_feeds', true) != false ? __('Yes', 'woocommerce-plugin-product-feeds') : __('No', 'woocommerce-plugin-product-feeds');
				break;
		}
	}
}
