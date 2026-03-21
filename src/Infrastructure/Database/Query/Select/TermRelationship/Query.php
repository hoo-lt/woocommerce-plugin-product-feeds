<?php

namespace Hoo\ProductFeeds\Infrastructure\Database\Query\Select\TermRelationship;

use Hoo\ProductFeeds\Domain;
use Hoo\WordPressPluginFramework\Database\Query\QueryException;
use Hoo\WordPressPluginFramework\Database\Query\Select\QueryInterface;

use wpdb;

class Query implements QueryInterface
{
	protected readonly string $query;

	public function __construct(
		protected readonly wpdb $wpdb,
		protected readonly string $path = __DIR__,
		protected readonly Domain\TermMeta $termMeta,
	) {
		$this->query = $this->query(
			$this->path(),
		);
	}

	public function __invoke(): string
	{
		return $this->wpdb->prepare($this->query, [
			$this->termMeta::KEY,
			$this->termMeta->value,
		]);
	}

	protected function path(): string
	{
		$path = "{$this->path}/Query.sql";
		if (!file_exists($path)) {
			throw new QueryException('.sql file not found');
		}

		return $path;
	}

	protected function query(string $path): string
	{
		return strtr(file_get_contents($path), [
			':term_taxonomy' => $this->wpdb->term_taxonomy,
			':termmeta' => $this->wpdb->termmeta,
			':term_relationships' => $this->wpdb->term_relationships,
		]);
	}
}