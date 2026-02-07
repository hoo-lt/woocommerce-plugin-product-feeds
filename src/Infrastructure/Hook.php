<?php

namespace Hoo\ProductFeeds\Infrastructure;

use Hoo\ProductFeeds\Application;
use Hoo\ProductFeeds\Domain;

class Hook
{
	public function __construct(
		protected readonly Application\Controllers\Term\ControllerInterface $termController,
	) {
	}

	public function __invoke()
	{
		wp_enqueue_style('product-feeds-admin', plugins_url('/assets/css/admin.css', __DIR__ . '/../../woocommerce-plugin-product-feeds.php'));

		foreach (Domain\Taxonomy::cases() as $taxonomy) {
			add_filter("manage_edit-{$taxonomy->value}_columns", fn($columns) => $columns += [
				'product_feeds' => esc_html__('Product feeds', 'woocommerce-plugin-product-feeds'),
			], PHP_INT_MAX, 1);
			add_filter("manage_{$taxonomy->value}_custom_column", fn($string, $column_name, $term_id) => match ($column_name) {
				'product_feeds' => $this->termController->template($term_id),
				default => $string,
			}, PHP_INT_MAX, 3);
			add_action("{$taxonomy->value}_add_form_fields", function ($taxonomy) {
				echo wp_kses_post($this->termController->addTemplate());
			}, PHP_INT_MAX, 1);
			add_action("{$taxonomy->value}_edit_form_fields", function ($tag, $taxonomy) {
				echo wp_kses_post($this->termController->editTemplate($tag->term_id));
			}, PHP_INT_MAX, 2);
			add_action("create_{$taxonomy->value}", fn($term_id) => $this->termController->add($term_id, $_POST['product_feeds']), PHP_INT_MAX, 1);
			add_action("edited_{$taxonomy->value}", fn($term_id) => $this->termController->edit($term_id, $_POST['product_feeds']), PHP_INT_MAX, 1);
		}
	}
}