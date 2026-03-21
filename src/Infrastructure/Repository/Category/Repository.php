<?php

namespace Hoo\ProductFeeds\Infrastructure\Repository\Category;

use Hoo\WordPressPluginFramework\Database\DatabaseInterface;
use Hoo\ProductFeeds\Domain;
use Hoo\ProductFeeds\Infrastructure;

class Repository implements Domain\Repository\Category\RepositoryInterface
{
	public function __construct(
		protected readonly DatabaseInterface $database,
		protected readonly Infrastructure\Database\Query\Select\Term\Query $selectTermQuery,
		protected readonly Infrastructure\Mapper\Category\Mapper $categoryMapper,
	) {
	}

	public function all(): Domain\Categories
	{
		return $this->categoryMapper->all($this->database->select($this->selectTermQuery));
	}
}