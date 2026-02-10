<?php

namespace Hoo\ProductFeeds\Infrastructure\Services\Product;

use Hoo\ProductFeeds\Infrastructure;

class Service
{
	public function __construct(
		protected readonly Infrastructure\Database\Database $database,
		protected readonly Infrastructure\Database\Queries\Product\Excluded\Query $excludedQuery,
		protected readonly Infrastructure\Database\Queries\Product\Simple\Query $simpleQuery,
	) {
	}

	public function __invoke()
	{
		$excluded = $this->database->select(
			$this->excludedQuery
		);

		$excludedIds = array_map(fn($excluded) => (int) $excluded['id'], $excluded);

		$simple = $this->database->select(
			$this->simpleQuery
				->excluded(...$excludedIds)
		);

		return $simple;
	}
}