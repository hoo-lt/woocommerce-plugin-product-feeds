<?php

namespace Hoo\ProductFeeds\Infrastructure\Database\Query\Select\Term;

use Hoo\WordPressPluginFramework\Database\Query\Select\QueryInterface;
use Hoo\ProductFeeds\Domain;
use wpdb;

class Query implements QueryInterface
{
	protected readonly string $query;

	public function __construct(
		protected readonly wpdb $wpdb,
		protected readonly string $path = __DIR__,
		protected readonly Domain\Taxonomy $taxonomy,
	) {
		$this->initialize();
	}

	public function __invoke(): string
	{
		return $this->wpdb->prepare($this->query, [
			$this->taxonomy->value,
		]);
	}

	protected function initialize(): void
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