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

use Hoo\WordPressPluginFramework\Hook\HookFactoryInterface;
use Hoo\WordPressPluginFramework\Hooker\HookerInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares;
use Hoo\WordPressPluginFramework\Router\RouterInterface;
use Hoo\WordPressPluginFramework\Database\Migrator\MigratorInterface;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Presentation;

$hooker = $container->get(HookerInterface::class);
$router = $container->get(RouterInterface::class);
$hookFactory = $container->get(HookFactoryInterface::class);
$migrator = $container->get(MigratorInterface::class);
$verifyNonce = $container->autowire(Middlewares\VerifyNonce\Middleware::class);
$termMetaController = $container->get(Presentation\Controllers\TermMeta\Controller::class);

$hooks = [
	$hookFactory->activation(__FILE__, function () use ($migrator, $router) {
		$migrator->up();
		$router->up();
	}),

	$hookFactory->deactivation(__FILE__, function () use ($migrator, $router) {
		$migrator->down();
		$router->down();
	}),

	$hookFactory->action(
		'admin_enqueue_scripts',
		fn() => wp_enqueue_style('product-feeds-admin', WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_URL . 'assets/css/admin.css')
	),
];

foreach (Domain\Taxonomy::cases() as $taxonomy) {
	$hooks = [
		...$hooks,

		$hookFactory->filter(
			"manage_edit-{$taxonomy->value}_columns",
			fn(array $columns) => $columns += [
				'product_feeds' => esc_html__('Product feeds', 'product-feeds')
			]
		),

		$hookFactory->filter(
			"manage_{$taxonomy->value}_custom_column",
			fn(string $string, string $column_name, int $term_id) => match ($column_name) {
				'product_feeds' => $termMetaController->index($term_id),
				default => $string,
			}
		),

		$hookFactory->action(
			"{$taxonomy->value}_add_form_fields",
			fn() => print $termMetaController->add()
		)
			->withMiddlewares(
				$container->autowire(Middlewares\CurrentUserCan\Middleware::class)
					->constructorParameter(
						'capability',
						Middlewares\CurrentUserCan\Capability\Capability::ManageWooCommerce,
					)
					->catch(fn() => ''),
			),

		$hookFactory->action(
			"{$taxonomy->value}_edit_form_fields",
			fn(WP_Term $tag) => print $termMetaController->edit($tag->term_id)
		)
			->withMiddlewares(
				$container->autowire(Middlewares\CurrentUserCan\Middleware::class)
					->constructorParameter(
						'capability',
						Middlewares\CurrentUserCan\Capability\Capability::ManageWooCommerce,
					)
					->catch(fn() => ''),
			),

		$hookFactory->action(
			"created_{$taxonomy->value}",
			fn(int $term_id) => $termMetaController->post($term_id)
		)
			->withMiddlewares(
				$container->autowire(Middlewares\VerifyNonce\Middleware::class)
					->constructorParameter(
						'action',
						'term_meta_controller_add',
					),
				$container->autowire(Middlewares\CurrentUserCan\Middleware::class)
					->constructorParameter(
						'capability',
						Middlewares\CurrentUserCan\Capability\Capability::ManageWooCommerce,
					),
				$container->get(Middlewares\ValidateRequest\Middleware::class)
					->post(Domain\TermMeta::KEY)->string(),
			),

		$hookFactory->action(
			"edited_{$taxonomy->value}",
			fn(int $term_id) => $termMetaController->post($term_id)
		)
			->withMiddlewares(
				$container->autowire(Middlewares\VerifyNonce\Middleware::class)
					->constructorParameter(
						'action',
						'term_meta_controller_edit',
					),
				$container->autowire(Middlewares\CurrentUserCan\Middleware::class)
					->constructorParameter(
						'capability',
						Middlewares\CurrentUserCan\Capability\Capability::ManageWooCommerce,
					),
				$container->get(Middlewares\ValidateRequest\Middleware::class)
					->post(Domain\TermMeta::KEY)->string(),
			),
	];
}

$hooker = $hooker->withHooks(
	...$hooks,
);

$hooker();
$router();