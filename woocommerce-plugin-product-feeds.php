<?php

/**
 * Plugin Name: Product feeds
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

require __DIR__ . '/vendor/autoload.php';

const PRODUCT_FEEDS = true;

use Hoo\ProductFeeds\Application;
use Hoo\ProductFeeds\Infrastructure;

use DI;

$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions([
	Application\Controllers\Term\ControllerInterface::class => DI\get(Application\Controllers\Term\Controller::class),
	Application\Mappers\Term\MapperInterface::class => DI\get(Infrastructure\Mappers\Term\Mapper::class),
	Application\Repositories\Term\RepositoryInterface::class => DI\get(Infrastructure\Repositories\Term\Repository::class),
	Application\TemplateInterface::class => DI\get(Infrastructure\Template::class),
]);

$container = $containerBuilder->build();
$container->get(Infrastructure\Hook::class)();