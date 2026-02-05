<?php
/**
 * Plugin Name: Product feeds
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

require __DIR__ . '/vendor/autoload.php';

use Hoo\ProductFeeds\Presentation\Controllers;

new Controllers\Controller(
	'edit-product_cat',
	'product_cat',
);