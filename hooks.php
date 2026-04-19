<?php

use Hoo\WordPressPluginFramework\{
	Database,
	Hooker,
	Pipeline,
	Router,
};
use Hoo\WooCommercePlugin\LtProductFeeds\{
	Domain,
	Presentation,
};

$hookFactory = $get(Hooker\Hooks\HookFactoryInterface::class);
$migrator = $get(Database\Migrator\MigratorInterface::class);
$router = $get(Router\RouterInterface::class);
$termMetaController = $get(Presentation\Controllers\TermMeta\Controller::class);
$verifyNonceMiddleware = $autowire(Pipeline\Middlewares\VerifyNonce\Middleware::class);
$currentUserCanMiddleware = $autowire(Pipeline\Middlewares\CurrentUserCan\Middleware::class);
$validateRequestMiddleware = $autowire(Pipeline\Middlewares\ValidateRequest\Middleware::class);

$hooks = [
	$hookFactory->activation(WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_FILE, function () use ($migrator, $router) {
		$migrator->up();
		$router->up();
	}),

	$hookFactory->deactivation(WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_FILE, function () use ($migrator, $router) {
		$migrator->down();
		$router->down();
	}),

	$hookFactory->action(
		'admin_enqueue_scripts',
		fn() => wp_enqueue_style('product-feeds-admin', WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_URL . 'assets/css/admin.css')
	),
];

foreach (Domain\Taxonomy::cases() as $taxonomy) {
	$hooks = [
		...$hooks,

		$hookFactory->filter(
			"manage_edit-{$taxonomy->value}_columns",
			fn(array $columns) => $columns += [
				'product_feeds' => esc_html__('Product feeds', 'product-feeds')
			]
		),

		$hookFactory->filter(
			"manage_{$taxonomy->value}_custom_column",
			fn(string $string, string $column_name, int $term_id) => match ($column_name) {
				'product_feeds' => $termMetaController->index($term_id),
				default => $string,
			}
		),

		$hookFactory->action(
			"{$taxonomy->value}_add_form_fields",
			fn() => print $termMetaController->add()
		)
			->withMiddlewares(
				$currentUserCanMiddleware
					->constructorParameter(
						'capability',
						Pipeline\Middlewares\CurrentUserCan\Capability\Capability::ManageWooCommerce,
					)
					->catch(fn() => ''),
			),

		$hookFactory->action(
			"{$taxonomy->value}_edit_form_fields",
			fn(WP_Term $tag) => print $termMetaController->edit($tag->term_id)
		)
			->withMiddlewares(
				$currentUserCanMiddleware
					->constructorParameter(
						'capability',
						Pipeline\Middlewares\CurrentUserCan\Capability\Capability::ManageWooCommerce,
					)
					->catch(fn() => ''),
			),

		$hookFactory->action(
			"created_{$taxonomy->value}",
			fn(int $term_id) => $termMetaController->post($term_id)
		)
			->withMiddlewares(
				$verifyNonceMiddleware
					->constructorParameter(
						'action',
						'term_meta_controller_add',
					),
				$currentUserCanMiddleware
					->constructorParameter(
						'capability',
						Pipeline\Middlewares\CurrentUserCan\Capability\Capability::ManageWooCommerce,
					),
				$validateRequestMiddleware
					->post(Domain\TermMeta::KEY)->string(),
			),

		$hookFactory->action(
			"edited_{$taxonomy->value}",
			fn(int $term_id) => $termMetaController->post($term_id)
		)
			->withMiddlewares(
				$verifyNonceMiddleware
					->constructorParameter(
						'action',
						'term_meta_controller_edit',
					),
				$currentUserCanMiddleware
					->constructorParameter(
						'capability',
						Pipeline\Middlewares\CurrentUserCan\Capability\Capability::ManageWooCommerce,
					),
				$validateRequestMiddleware
					->post(Domain\TermMeta::KEY)->string(),
			),
	];
}

return $hooks;