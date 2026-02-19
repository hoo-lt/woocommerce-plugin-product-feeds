<?php

namespace Hoo\ProductFeeds\Infrastructure\Database\Queries\Select\Brand;

use Hoo\ProductFeeds\Infrastructure;

use wpdb;

class Query implements Infrastructure\Database\Queries\Select\QueryInterface
{
	protected readonly string $query;
	protected readonly array $args;

	public function __construct(
		protected readonly wpdb $wpdb,
		protected readonly string $path = __DIR__,
	) {
		$this->initializeQuery();
		$this->initializeArgs();
	}

	public function __invoke(): string
	{
		return $this->wpdb->prepare($this->query, $this->args);
	}

	protected function initializeQuery(): void
	{
		$path = "{$this->path}/Query.sql";
		if (!file_exists($path)) {
			//throw exception
		}

		$this->query = strtr(file_get_contents($path), [
			':term_taxonomy' => $this->wpdb->term_taxonomy,
			':terms' => $this->wpdb->terms,
		]);
	}

	protected function initializeArgs(): void
	{
		$this->args = [
			rtrim(home_url(), '/'),
			get_option('woocommerce_brand_permalink') ?? '',
		];
	}
}