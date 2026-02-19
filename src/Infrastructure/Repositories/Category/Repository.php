<?php

namespace Hoo\ProductFeeds\Infrastructure\Repositories\Category;

use Hoo\ProductFeeds\Domain;
use Hoo\ProductFeeds\Infrastructure;

class Repository
{
	public function __construct(
		protected readonly Infrastructure\Database\DatabaseInterface $database,
		protected readonly Infrastructure\Database\Queries\Select\Category\Query $selectCategoryQuery,
		protected readonly Infrastructure\Mappers\Category\Mapper $categoryMapper,
	) {
	}

	public function all(): Domain\Categories
	{
		return $this->categoryMapper->all($this->database->select($this->selectCategoryQuery));
	}
}