<?php

namespace Hoo\ProductFeeds\Infrastructure;

use Hoo\ProductFeeds\Application;
use Hoo\ProductFeeds\Domain;

use WP_Term;

class Hook
{
	public function __construct(
		protected readonly Application\Controllers\Term\ControllerInterface $termController,
	) {
	}

	public function __invoke()
	{
		wp_enqueue_style('product-feeds-admin', plugins_url('/assets/css/admin.css', __DIR__ . '/../../woocommerce-product-feeds.php'));

		foreach (Domain\Taxonomy::cases() as $taxonomy) {
			add_filter("manage_edit-{$taxonomy->value}_columns", [
				$this,
				'manage_edit_taxonomy_columns'
			], PHP_INT_MAX, 1);
			add_filter("manage_{$taxonomy->value}_custom_column", [
				$this,
				'manage_taxonomy_custom_column'
			], PHP_INT_MAX, 3);
			add_action("{$taxonomy->value}_add_form_fields", [
				$this,
				'taxonomy_add_form_fields'
			], PHP_INT_MAX, 1);
			add_action("{$taxonomy->value}_edit_form_fields", [
				$this,
				'taxonomy_edit_form_fields'
			], PHP_INT_MAX, 2);
			add_action("created_{$taxonomy->value}", [
				$this,
				'created_taxonomy'
			], PHP_INT_MAX, 3);
			add_action("edited_{$taxonomy->value}", [
				$this,
				'edited_taxonomy'
			], PHP_INT_MAX, 3);
		}
	}

	public function manage_edit_taxonomy_columns(array $columns): array
	{
		return $columns += [
			'product_feeds' => esc_html__('Product feeds', 'product-feeds'),
		];
	}

	public function manage_taxonomy_custom_column(string $string, string $column_name, int $term_id): string
	{
		return match ($column_name) {
			'product_feeds' => $this->termController->template($term_id),
			default => $string,
		};
	}

	public function taxonomy_add_form_fields(string $taxonomy): void
	{
		echo $this->termController->addTemplate();
	}

	public function taxonomy_edit_form_fields(WP_Term $tag, string $taxonomy): void
	{
		echo $this->termController->editTemplate($tag->term_id);
	}

	public function created_taxonomy(int $term_id, int $tt_id, array $args): void
	{
		$this->termController->add($term_id, $_POST['product_feeds']);
	}

	public function edited_taxonomy(int $term_id, int $tt_id, array $args): void
	{
		$this->termController->edit($term_id, $_POST['product_feeds']);
	}
}