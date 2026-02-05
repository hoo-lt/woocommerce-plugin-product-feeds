<?php
/**
 * Plugin Name: Product feeds
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

require __DIR__ . '/vendor/autoload.php';

use Hoo\ProductFeeds\Presentation;
use Hoo\ProductFeeds\Infrastructure;

$taxonomyController = new Presentation\Taxonomy\Controller(new Infrastructure\Term\Repository);
$taxonomyController('product_brand');
$taxonomyController('product_cat');