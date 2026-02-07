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

const WOOCOMMERCE_PRODUCT_FEEDS = true;

use Hoo\ProductFeeds\Application;
use Hoo\ProductFeeds\Infrastructure;

$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions([
	Application\Controllers\Term\ControllerInterface::class => DI\get(Application\Controllers\Term\Controller::class),
	Application\Mappers\Term\MapperInterface::class => DI\get(Infrastructure\Mappers\Term\Mapper::class),
	Application\Repositories\Term\RepositoryInterface::class => DI\get(Infrastructure\Repositories\Term\Repository::class),
	Application\TemplateInterface::class => DI\get(Infrastructure\Template::class),
	Infrastructure\Clients\ClientInterface::class => DI\get(Infrastructure\Clients\Client::class),
	Infrastructure\Hooks\ActionHooks::class => DI\create()
		->constructor(
			DI\get(Application\Controllers\Feed\Kaina24Lt\Controller::class),
			DI\get(Application\Controllers\Feed\KainosLt\Controller::class),
			DI\get(Application\Controllers\Feed\KainotekaLt\Controller::class)
		),

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