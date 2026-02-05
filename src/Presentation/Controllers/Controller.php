<?php

namespace Hoo\ProductFeeds\Presentation\Controllers;

class Controller
{
	public function __construct(
		protected readonly string $id,
		protected readonly string $taxonomy,
	) {
		add_filter("manage_{$this->id}_columns", fn($columns) => $this->add($columns));
		add_filter("manage_{$this->taxonomy}_custom_column", fn($string, $column_name, $term_id) => $this->add2($string, $column_name, $term_id));
	}

	protected function add(array $columns)
	{
		$columns['product_feeds'] = __('Product feeds', 'woocommerce-plugin-product-feeds');
		return $columns;
	}

	protected function add2(string $string, string $column_name, int $term_id)
	{
		return match ($column_name) {
			'product_feeds' => get_term_meta($term_id, '_product_feeds', true) != false ? __('Yes', 'woocommerce-plugin-product-feeds') : __('No', 'woocommerce-plugin-product-feeds'),
			default => $string,
		};
	}
}
