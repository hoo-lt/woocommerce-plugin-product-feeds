<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Repositories\Category;

use Hoo\WordPressPluginFramework\Database\SelectInterface;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure;

readonly class Repository implements Domain\Repository\Category\RepositoryInterface
{
	public function __construct(
		protected SelectInterface $select,
		protected Infrastructure\Database\Queries\Select\Term\Query $termQuery,
		protected Infrastructure\Mapper\Category\Mapper $categoryMapper,
	) {
	}

	public function all(): Domain\Categories
	{
		return $this->categoryMapper->all(($this->select)($this->termQuery));
	}
}