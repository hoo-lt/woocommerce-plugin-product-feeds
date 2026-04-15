<?php

use Hoo\WordPressPluginFramework;
use Hoo\WooCommercePluginFramework;

use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure;
use Hoo\WooCommercePlugin\LtProductFeeds\Presentation;

return [
		/**
		 * WordPress Plugin Framework
		 */
	WordPressPluginFramework\Cache\CacheInterface::class => DI\get(WordPressPluginFramework\Cache\Cache::class),
	WordPressPluginFramework\Database\DatabaseInterface::class => DI\get(WordPressPluginFramework\Database\Database::class),
	WordPressPluginFramework\Json\JsonInterface::class => DI\get(WordPressPluginFramework\Json\Json::class),
	WordPressPluginFramework\Pipeline\PipelineInterface::class => DI\get(WordPressPluginFramework\Pipeline\Pipeline::class),
	WordPressPluginFramework\View\ViewInterface::class => DI\autowire(WordPressPluginFramework\View\View::class)
		->constructorParameter(
			'path',
			WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_PATH . '/src/Presentation/View'
		),
	WordPressPluginFramework\Logger\LoggerInterface::class => DI\autowire(WooCommercePluginFramework\Logger\Logger::class)
		->constructorParameter(
			'source',
			'product-feeds'
		),
	WordPressPluginFramework\Middlewares\VerifyNonce\Middleware::class => DI\autowire()
		->constructorParameter(
			'nonceName',
			'product_feeds_nonce'
		),

	WordPressPluginFramework\Http\RequestInterface::class => DI\factory(fn() => new WordPressPluginFramework\Http\Request(
		$_GET,
		$_POST,
	)),

	WordPressPluginFramework\Database\Migrator\MigratorRepositoryInterface::class => DI\get(WordPressPluginFramework\Database\Migrator\MigratorRepository::class),
	WordPressPluginFramework\Database\Migrator\MigratorInterface::class => DI\autowire(WordPressPluginFramework\Database\Migrator\Migrator::class)
		->constructorParameter(
			'path',
			__DIR__ . '/src/Infrastructure/Database/Migration'
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
	Infrastructure\Mapper\Brand\Mapper::class => DI\autowire()
		->constructorParameter(
			'url',
			site_url()
		)
		->constructorParameter(
			'path',
			'/' . ltrim(get_option('woocommerce_brand_permalink'), '/') ?? ''
		),
	Infrastructure\Mapper\Category\Mapper::class => DI\autowire()
		->constructorParameter(
			'url',
			site_url()
		)
		->constructorParameter(
			'path',
			'/' . ltrim(get_option('woocommerce_permalinks')['category_base'], '/') ?? ''
		),
	Infrastructure\Mapper\Product\Mapper::class => DI\autowire()
		->constructorParameter(
			'url',
			site_url()
		)
		->constructorParameter(
			'path',
			'/' . ltrim(get_option('woocommerce_permalinks')['product_base'], '/') ?? ''
		),
	Infrastructure\Mapper\Tag\Mapper::class => DI\autowire()
		->constructorParameter(
			'url',
			site_url()
		)
		->constructorParameter(
			'path',
			'/' . ltrim(get_option('woocommerce_permalinks')['tag_base'], '/') ?? ''
		),

	/**
	 * Hooks
	 */
	/*
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
	*/

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
	wpdb::class => DI\factory(function () {
		global $wpdb;
		return $wpdb;
	}),

	/**
	 * WooCommerce
	 */
	WC_Logger_Interface::class => DI\factory(fn() => wc_get_logger()),

	XMLWriter::class => DI\factory(fn() => new XMLWriter()),
];