<?php

namespace Hoo\ProductFeeds\Infrastructure\Repositories\Brand;

use Hoo\ProductFeeds\Domain;
use Hoo\ProductFeeds\Infrastructure;

class Repository
{
	public function __construct(
		protected readonly Infrastructure\Database\DatabaseInterface $database,
		protected readonly Infrastructure\Database\Queries\Select\Brand\Query $selectBrandQuery,
		protected readonly Infrastructure\Mappers\Brand\Mapper $brandMapper,
	) {
	}

	public function all(): Domain\Brands
	{
		return $this->brandMapper->all($this->database->select($this->selectBrandQuery));
	}
}