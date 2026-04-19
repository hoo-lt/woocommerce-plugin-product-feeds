<?php

use Hoo\WordPressPluginFramework;
use Hoo\WooCommercePluginFramework;

use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure;
use Hoo\WooCommercePlugin\LtProductFeeds\Presentation;

global $wpdb;

return [
		/**
		 * WordPress Plugin Framework
		 */
	WordPressPluginFramework\Cache\CacheInterface::class => DI\get(WordPressPluginFramework\Cache\Cache::class),

	WordPressPluginFramework\Database\Migrator\MigratorInterface::class => DI\autowire(WordPressPluginFramework\Database\Migrator\Migrator::class)
		->constructorParameter(
			'path',
			WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_PATH,
		),
	WordPressPluginFramework\Database\Table\TableInterface::class => DI\autowire(WordPressPluginFramework\Database\Table\Table::class)
		->constructorParameter(
			'prefix',
			$wpdb->prefix,
		),
	WordPressPluginFramework\Database\SelectInterface::class => DI\get(WordPressPluginFramework\Database\Select::class),
	WordPressPluginFramework\Http\Request\RequestInterface::class => DI\factory(fn() => new WordPressPluginFramework\Http\Request\Request(
		DI\get(WordPressPluginFramework\Json\JsonInterface::class),
		$_SERVER,
		$_GET,
		$_POST,
		file_get_contents('php://input') ?: '',
	)),
	WordPressPluginFramework\Json\JsonInterface::class => DI\get(WordPressPluginFramework\Json\Json::class),
	WordPressPluginFramework\Loggers\LoggerInterface::class => DI\autowire(WooCommercePluginFramework\Loggers\Logger::class)
		->constructorParameter(
			'source',
			'product-feeds',
		),
	WordPressPluginFramework\Pipeline\Middlewares\VerifyNonce\Middleware::class => DI\autowire()
		->constructorParameter(
			'name',
			'product_feeds_nonce',
		),
	WordPressPluginFramework\Pipeline\PipelineInterface::class => DI\get(WordPressPluginFramework\Pipeline\Pipeline::class),
	WordPressPluginFramework\Hooker\Hooks\HookFactoryInterface::class => DI\get(WordPressPluginFramework\Hooker\Hooks\HookFactory::class),
	WordPressPluginFramework\Router\Routes\RouteFactoryInterface::class => DI\autowire(WordPressPluginFramework\Router\Routes\RouteFactory::class)
		->constructorParameter(
			'namespace',
			'lt-product-feeds/v1',
		),
	WordPressPluginFramework\Hooker\HookerInterface::class => DI\get(WordPressPluginFramework\Hooker\Hooker::class),
	WordPressPluginFramework\Router\RouterInterface::class => DI\autowire(WordPressPluginFramework\Router\Router::class),
	WordPressPluginFramework\Repositories\Database\Migrator\RepositoryInterface::class => DI\autowire(WordPressPluginFramework\Repositories\Database\Migrator\Repository::class)
		->constructorParameter(
			'key',
			'lt_product_feeds_migrations',
		),
	WordPressPluginFramework\View\ViewInterface::class => DI\autowire(WordPressPluginFramework\View\View::class)
		->constructorParameter(
			'path',
			WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_PATH,
		),

		/**
		 * Repositories
		 */
	Domain\Repository\Attribute\RepositoryInterface::class => DI\get(Infrastructure\Repositories\Attribute\Repository::class),
	Domain\Repository\Brand\RepositoryInterface::class => DI\autowire(Infrastructure\Repositories\Brand\Repository::class)
		->constructorParameter(
			'selectTermQuery',
			DI\autowire(Infrastructure\Database\Queries\Select\Term\Query::class)
				->constructorParameter(
					'taxonomy',
					Domain\Taxonomy::Brand
				)
		),
	Domain\Repository\Category\RepositoryInterface::class => DI\autowire(Infrastructure\Repositories\Category\Repository::class)
		->constructorParameter(
			'selectTermQuery',
			DI\autowire(Infrastructure\Database\Queries\Select\Term\Query::class)
				->constructorParameter(
					'taxonomy',
					Domain\Taxonomy::Category
				)
		),
	Domain\Repository\Product\RepositoryInterface::class => DI\get(Infrastructure\Repositories\Product\Repository::class),
	Domain\Repository\Tag\RepositoryInterface::class => DI\autowire(Infrastructure\Repositories\Tag\Repository::class)
		->constructorParameter(
			'selectTermQuery',
			DI\autowire(Infrastructure\Database\Queries\Select\Term\Query::class)
				->constructorParameter(
					'taxonomy',
					Domain\Taxonomy::Tag
				)
		),
	Domain\Repository\TermMeta\RepositoryInterface::class => DI\get(Infrastructure\Repositories\TermMeta\Repository::class),
	Domain\Repository\TermRelationship\RepositoryInterface::class => DI\get(Infrastructure\Repositories\TermRelationship\Repository::class),

	Infrastructure\Database\Queries\Select\TermRelationship\Query::class => DI\autowire()
		->constructorParameter(
			'termMeta',
			Domain\TermMeta::Excluded
		),

		/**
		 * Mappers
		 */
	Infrastructure\Mappers\Brand\Mapper::class => DI\autowire()
		->constructorParameter(
			'url',
			site_url()
		)
		->constructorParameter(
			'path',
			'/' . ltrim(get_option('woocommerce_brand_permalink'), '/') ?? ''
		),
	Infrastructure\Mappers\Category\Mapper::class => DI\autowire()
		->constructorParameter(
			'url',
			site_url()
		)
		->constructorParameter(
			'path',
			'/' . ltrim(get_option('woocommerce_permalinks')['category_base'], '/') ?? ''
		),
	Infrastructure\Mappers\Product\Simple\Mapper::class => DI\autowire()
		->constructorParameter(
			'url',
			site_url()
		)
		->constructorParameter(
			'path',
			'/' . ltrim(get_option('woocommerce_permalinks')['product_base'], '/') ?? ''
		),
	Infrastructure\Mappers\Tag\Mapper::class => DI\autowire()
		->constructorParameter(
			'url',
			site_url()
		)
		->constructorParameter(
			'path',
			'/' . ltrim(get_option('woocommerce_permalinks')['tag_base'], '/') ?? ''
		),

	/**
	 * Controllers
	 */
	/*
	Presentation\Mapper\Feed\Kaina24Lt\Mapper::class => DI\autowire()
		->constructorParameter('utmSource', 'kaina24.lt')
		->constructorParameter('utmMedium', 'ppc'),

	Presentation\Presenters\Feed\Kaina24Lt\Presenter::class => DI\autowire()
		->constructorParameter('path', 'kaina24-lt.xml'),
	*/

	/**
	 * WordPress
	 */
	wpdb::class => DI\factory(fn() => $wpdb),

	/**
	 * WooCommerce
	 */
	WC_Logger_Interface::class => DI\factory(fn() => wc_get_logger()),

	XMLWriter::class => DI\factory(fn() => new XMLWriter()),
];