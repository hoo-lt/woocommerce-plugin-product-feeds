<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Database\Query\Select\Product\Simple;

use Hoo\WordPressPluginFramework\Database\Query\Select\QueryInterface;
use Hoo\WordPressPluginFramework\Database\Query\QueryException;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use wpdb;

readonly class Query implements QueryInterface
{
	protected string $query;

	public function __construct(
		protected wpdb $wpdb,
		protected array $ids = [],
		protected array $statuses = [],
	) {
		$this->query = $this->query(
			$this->path(),
		);
	}

	public function withIds(int ...$ids): self
	{
		return new self(
			$this->wpdb,
			$ids,
			$this->statuses,
		);
	}

	public function withStatuses(Domain\Products\Product\Status ...$statuses): self
	{
		return new self(
			$this->wpdb,
			$this->ids,
			$statuses,
		);
	}

	public function __invoke(): string
	{
		return $this->wpdb->prepare(strtr($this->query, [
			':AND posts.ID' => $this->ids ? 'AND posts.ID IN (' . implode(',', array_map(fn() => '%d', $this->ids)) . ')' : '',
			':AND posts.post_status' => $this->statuses ? 'AND posts.post_status IN (' . implode(',', array_map(fn() => '%s', $this->statuses)) . ')' : '',
		]), [
			...$this->ids,
			...array_map(fn($status) => $status->value, $this->statuses),
		]);
	}

	protected function path(): string
	{
		$path = __DIR__ . '/Query.sql';
		if (!file_exists($path)) {
			throw new QueryException('.sql file not found');
		}

		return $path;
	}

	protected function query(string $path): string
	{
		return strtr(file_get_contents($path), [
			':term_relationships' => $this->wpdb->term_relationships,
			':posts' => $this->wpdb->posts,
			':term_taxonomy' => $this->wpdb->term_taxonomy,
			':terms' => $this->wpdb->terms,
			':postmeta' => $this->wpdb->postmeta,
		]);
	}
}