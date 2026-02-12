<?php

namespace Hoo\ProductFeeds\Infrastructure\Hooks;

use Hoo\ProductFeeds\Presentation;

class ActionHooks
{
	protected readonly array $feedPresenters;

	public function __construct(
		Presentation\Presenters\Feed\PresenterInterface ...$feedPresenters,
	) {
		$this->feedPresenters = $feedPresenters;
	}

	public function __invoke(): void
	{
		add_action('admin_enqueue_scripts', [
			$this,
			'admin_enqueue_scripts'
		], PHP_INT_MAX, 0);

		add_action('init', [
			$this,
			'add_feeds'
		], PHP_INT_MAX, 0);
	}

	public function admin_enqueue_scripts(): void
	{
		wp_enqueue_style(
			'product-feeds-admin',
			plugins_url('assets/css/admin.css', WOOCOMMERCE_PRODUCT_FEEDS_PLUGIN_URL)
		);
	}

	public function add_feeds(): void
	{
		foreach ($this->feedPresenters as $feedPresenter) {
			add_feed($feedPresenter->path(), function () use ($feedPresenter) {
				echo $feedPresenter->present();
			});
		}
	}
}