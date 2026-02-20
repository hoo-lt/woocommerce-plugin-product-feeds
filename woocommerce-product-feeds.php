<?php

/**
 * Plugin Name: WooCommerce product feeds by HOO
 * Plugin URI: https://github.com/hoo-lt/woocommerce-product-feeds
 * Description:
 * Version: 1.0.0
 * Requires at least: 6.9
 * Requires PHP: 8.2
 * Author: Baltic digital agency, UAB
 * Author URI: https://github.com/hoo-lt
 * License: GPL-3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: woocommerce-product-feeds
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 */

if (!defined('ABSPATH')) {
	die();
}

require __DIR__ . '/vendor/autoload.php';

define('WOOCOMMERCE_PRODUCT_FEEDS', true);
define('WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_PATH', plugin_dir_path(__FILE__));

use Hoo\ProductFeeds\Presentation;
use Hoo\ProductFeeds\Domain;
use Hoo\ProductFeeds\Infrastructure;

$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions([
	Presentation\View\ViewInterface::class => DI\get(Presentation\View\View::class),

	Domain\Repositories\Brand\RepositoryInterface::class => DI\get(Infrastructure\Repositories\Brand\Repository::class),
	Domain\Repositories\Category\RepositoryInterface::class => DI\get(Infrastructure\Repositories\Category\Repository::class),
	Domain\Repositories\Product\RepositoryInterface::class => DI\get(Infrastructure\Repositories\Product\Repository::class),
	Domain\Repositories\TermMeta\RepositoryInterface::class => DI\get(Infrastructure\Repositories\TermMeta\Repository::class),

	Infrastructure\Database\DatabaseInterface::class => DI\get(Infrastructure\Database\Database::class),

	Infrastructure\Database\Queries\Select\Brand\Query::class => DI\autowire()
		->constructorParameter('homeUrl', rtrim(home_url(), '/'))
		->constructorParameter('permalink', get_option('woocommerce_brand_permalink') ?? ''),

	Infrastructure\Database\Queries\Select\Category\Query::class => DI\autowire()
		->constructorParameter('homeUrl', rtrim(home_url(), '/'))
		->constructorParameter('permalink', get_option('woocommerce_permalinks')['category_base'] ?? ''),

	Infrastructure\Database\Queries\Select\Tag\Query::class => DI\autowire()
		->constructorParameter('homeUrl', rtrim(home_url(), '/'))
		->constructorParameter('permalink', get_option('woocommerce_permalinks')['tag_base'] ?? ''),

	Infrastructure\Hooks\ActionHooks::class => DI\factory(fn(DI\Container $container) => new Infrastructure\Hooks\ActionHooks(...[
		...[
			$container->get(Presentation\Presenters\Feed\Kaina24Lt\Presenter::class),
		],
		...apply_filters('woocommerce_product_feeds_add_feed_presenters', []),
	])),

	wpdb::class => DI\factory(function (): wpdb {
		global $wpdb;
		return $wpdb;
	}),
]);

$container = $containerBuilder->build();

$actionHooks = $container->get(Infrastructure\Hooks\ActionHooks::class);
$actionHooks();

$filterHooks = $container->get(Infrastructure\Hooks\FilterHooks::class);
$filterHooks();

register_activation_hook(__FILE__, function () {
	flush_rewrite_rules();
});