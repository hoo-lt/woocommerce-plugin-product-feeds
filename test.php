<?php

use Hoo\WordPressPluginFramework\Database\DatabaseInterface;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure;
use Hoo\WooCommercePlugin\LtProductFeeds\Presentation;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/../../../wp-load.php';

define('WOOCOMMERCE_PRODUCT_FEEDS', true);
define('WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_PATH', plugin_dir_path(__FILE__));

$container = require __DIR__ . '/container.php';

$database = $container->get(DatabaseInterface::class);
$query = $container->get(Infrastructure\Database\Query\Select\Product\Simple\Query::class);

$rows = $database->json($query);

$mapper = new Infrastructure\Mapper\Product\Simple\Mapper(
	site_url(),
	'/' . ltrim(get_option('woocommerce_permalinks')['product_base'], '/'),
);

$products = $mapper->all($rows);

print_r($products);