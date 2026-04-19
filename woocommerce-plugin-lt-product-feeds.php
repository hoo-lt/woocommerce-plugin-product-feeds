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
define('WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_FILE', __FILE__);
define('WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_PATH', plugin_dir_path(__FILE__));

require __DIR__ . '/vendor/autoload.php';

use Hoo\WordPressPluginFramework\Hooker;
use Hoo\WordPressPluginFramework\Router;

$container = require __DIR__ . '/container.php';

$autowire = $container->autowire(...);
$get = $container->get(...);

$hooker = $get(Hooker\HookerInterface::class);
$hooker = $hooker->withHooks(
	...require __DIR__ . '/hooks.php',
);

$router = $get(Router\RouterInterface::class);
$router = $router->withRoutes(
	...require __DIR__ . '/routes.php',
);

$hooker();
$router();