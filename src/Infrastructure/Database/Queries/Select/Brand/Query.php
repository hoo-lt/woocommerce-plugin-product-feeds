<?php

namespace Hoo\ProductFeeds\Infrastructure\Database\Queries\Select\Brand;

use Hoo\ProductFeeds\Infrastructure;

use wpdb;

class Query implements Infrastructure\Database\Queries\Select\QueryInterface
{
	protected readonly string $query;

	public function __construct(
		protected readonly wpdb $wpdb,
		protected readonly string $path = __DIR__,
	) {
		$this->initializeQuery();
	}

	public function __invoke(): string
	{
		$home_url = rtrim(home_url(), '/');

		$woocommerce_brand_permalink = get_option('woocommerce_brand_permalink') ?? '';

		return $this->wpdb->prepare($this->query, [
			$home_url,
			$woocommerce_brand_permalink,
		]);
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
}