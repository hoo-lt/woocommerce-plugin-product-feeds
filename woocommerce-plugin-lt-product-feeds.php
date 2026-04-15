<?php

/**
 * Plugin Name: LT Product Feeds for WooCommerce
 * Plugin URI: https://github.com/hoo-lt/woocommerce-plugin-lt-product-feeds
 * Description:
 * Version: 1.0.0
 * Requires at least: 6.9
 * Requires PHP: 8.2
 * Author: Baltic digital agency, UAB
 * Author URI: https://github.com/hoo-lt
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: lt-product-feeds
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 */

if (!defined('ABSPATH')) {
	die();
}

define('WOOCOMMERCE_PRODUCT_FEEDS', true);
define('WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_PATH', plugin_dir_path(__FILE__));

require __DIR__ . '/vendor/autoload.php';

$container = require __DIR__ . '/container.php';

use Hoo\WordPressPluginFramework;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Presentation;

$router = $container->get(WordPressPluginFramework\Router\Router::class);
$verifyNonce = $container->get(WordPressPluginFramework\Middlewares\VerifyNonce\Middleware::class);
$termPresenter = $container->get(Presentation\Presenters\Term\Presenter::class);

add_action('admin_enqueue_scripts', fn() =>
	wp_enqueue_style('product-feeds-admin', WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_URL . 'assets/css/admin.css')
);

foreach (Domain\Taxonomy::cases() as $taxonomy) {
	$router->addRoutes(
		WordPressPluginFramework\Route\Route::filter("manage_edit-{$taxonomy->value}_columns", fn(array $columns) =>
			$columns += ['product_feeds' => esc_html__('Product feeds', 'product-feeds')]
		),

		WordPressPluginFramework\Route\Route::filter("manage_{$taxonomy->value}_custom_column", fn(string $string, string $column_name, int $term_id) =>
			match ($column_name) {
				'product_feeds' => $termPresenter->view($term_id),
				default => $string,
			}
		),

		WordPressPluginFramework\Route\Route::action("{$taxonomy->value}_add_form_fields", fn() =>
			print $termPresenter->addView()
		),

		WordPressPluginFramework\Route\Route::action("{$taxonomy->value}_edit_form_fields", fn(WP_Term $tag) =>
			print $termPresenter->editView($tag->term_id)
		),

		WordPressPluginFramework\Route\Route::action("created_{$taxonomy->value}", fn(int $term_id) =>
			$termPresenter->save($term_id)
		)->withMiddlewares($verifyNonce),

		WordPressPluginFramework\Route\Route::action("edited_{$taxonomy->value}", fn(int $term_id) =>
			$termPresenter->save($term_id)
		)->withMiddlewares($verifyNonce),
	);
}

$router();

register_activation_hook(__FILE__, function () use ($container) {
	$migrator = $container->get(Hoo\WordPressPluginFramework\Database\Migrator\MigratorInterface::class);
	$migrator->migrate();

	flush_rewrite_rules();
});