<?php

/**
 * Plugin Name: LT Product Feeds for WooCommerce
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

use Hoo\WordPressPluginFramework;
use Hoo\WooCommercePluginFramework;

use Hoo\ProductFeeds\Presentation;
use Hoo\ProductFeeds\Domain;
use Hoo\ProductFeeds\Infrastructure;

$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions([
	WordPressPluginFramework\Cache\CacheInterface::class => DI\get(WordPressPluginFramework\Cache\Cache::class),
	WordPressPluginFramework\Database\DatabaseInterface::class => DI\get(WordPressPluginFramework\Database\Database::class),
	WordPressPluginFramework\Pipeline\PipelineInterface::class => DI\get(WordPressPluginFramework\Pipeline\Pipeline::class),
	WordPressPluginFramework\View\ViewInterface::class => DI\autowire(WordPressPluginFramework\View\View::class)
		->constructorParameter('path', WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_PATH . '/src/Presentation/View'),
	WordPressPluginFramework\Logger\LoggerInterface::class => DI\autowire(WooCommercePluginFramework\Logger\Logger::class)
		->constructorParameter('source', 'product-feeds'),
	WordPressPluginFramework\Middleware\VerifyNonce\Middleware::class => DI\autowire()
		->constructorParameter('nonceName', 'product_feeds_nonce'),

	WordPressPluginFramework\Http\RequestInterface::class => DI\factory(fn() => new WordPressPluginFramework\Http\Request(
		$_GET,
		$_POST,
	)),

	Domain\Repository\Attribute\RepositoryInterface::class => DI\get(Infrastructure\Repository\Attribute\Repository::class),
	Domain\Repository\Brand\RepositoryInterface::class => DI\get(Infrastructure\Repository\Brand\Repository::class),
	Domain\Repository\Category\RepositoryInterface::class => DI\get(Infrastructure\Repository\Category\Repository::class),
	Domain\Repository\Product\RepositoryInterface::class => DI\get(Infrastructure\Repository\Product\Repository::class),
	Domain\Repository\Term\RepositoryInterface::class => DI\get(Infrastructure\Repository\Term\Repository::class),
	Domain\Repository\TermMeta\RepositoryInterface::class => DI\get(Infrastructure\Repository\TermMeta\Repository::class),

	Domain\Repository\Brand\RepositoryInterface::class => DI\autowire(Infrastructure\Repository\Brand\Repository::class)
		->constructorParameter('selectTermQuery', DI\create(Infrastructure\Database\Query\Select\Term\Query::class)
			->constructorParameter('taxonomy', Domain\Taxonomy::Brand)),

	Domain\Repository\Category\RepositoryInterface::class => DI\autowire(Infrastructure\Repository\Category\Repository::class)
		->constructorParameter('selectTermQuery', DI\create(Infrastructure\Database\Query\Select\Term\Query::class)
			->constructorParameter('taxonomy', Domain\Taxonomy::Category)),

	Domain\Repository\Tag\RepositoryInterface::class => DI\autowire(Infrastructure\Repository\Tag\Repository::class)
		->constructorParameter('selectTermQuery', DI\create(Infrastructure\Database\Query\Select\Term\Query::class)
			->constructorParameter('taxonomy', Domain\Taxonomy::Tag)),

	Infrastructure\Database\Query\Select\TermRelationship\Query::class => DI\autowire()
		->constructorParameter('termMeta', Domain\TermMeta::Excluded),

	Infrastructure\Mapper\Brand\Mapper::class => DI\autowire()
		->constructorParameter('url', site_url())
		->constructorParameter('path', '/' . ltrim(get_option('woocommerce_brand_permalink'), '/') ?? ''),
	Infrastructure\Mapper\Category\Mapper::class => DI\autowire()
		->constructorParameter('url', site_url())
		->constructorParameter('path', '/' . ltrim(get_option('woocommerce_permalinks')['category_base'], '/') ?? ''),
	Infrastructure\Mapper\Tag\Mapper::class => DI\autowire()
		->constructorParameter('url', site_url())
		->constructorParameter('path', '/' . ltrim(get_option('woocommerce_permalinks')['tag_base'], '/') ?? ''),

	Infrastructure\Database\Query\Select\Product\Simple\Query::class => DI\autowire()
		->constructorParameter('homeUrl', rtrim(home_url(), '/'))
		->constructorParameter('permalink', trim(get_option('woocommerce_permalinks')['product_base'], '/') ?? ''),

	Infrastructure\Hook\Action\Hook::class => DI\factory(function (DI\Container $container) {
		$pipeline = $container->get(WordPressPluginFramework\Pipeline\PipelineInterface::class);
		$feedPresenters = array_map($container->get(...), [
			...[
				Presentation\Presenters\Feed\Kaina24Lt\Presenter::class,
			],
			...apply_filters('woocommerce_product_feeds_add_feed_presenters', []),
		]);

		return new Infrastructure\Hook\Action\Hook(
			$pipeline,
			...$feedPresenters
		);
	}),

	Presentation\Mapper\Feed\Kaina24Lt\Mapper::class => DI\autowire()
		->constructorParameter('utmSource', 'kaina24.lt')
		->constructorParameter('utmMedium', 'ppc'),

	Presentation\Presenters\Feed\Kaina24Lt\Presenter::class => DI\autowire()
		->constructorParameter('path', 'kaina24-lt.xml'),

	wpdb::class => DI\factory(function () {
		global $wpdb;
		return $wpdb;
	}),

	XMLWriter::class => DI\factory(fn() => new XMLWriter()),

	WC_Logger_Interface::class => DI\factory(fn() => wc_get_logger()),
]);

$container = $containerBuilder->build();

$actionHook = $container->get(Infrastructure\Hook\Action\Hook::class);
$actionHook();

$filterHook = $container->get(Infrastructure\Hook\Filter\Hook::class);
$filterHook();

register_activation_hook(__FILE__, function () {
	flush_rewrite_rules();
});