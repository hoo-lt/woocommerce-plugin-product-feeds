<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Repositories\Tag;

use Hoo\WordPressPluginFramework\Database\SelectInterface;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure;

readonly class Repository implements Domain\Repository\Tag\RepositoryInterface
{
	public function __construct(
		protected SelectInterface $select,
		protected Infrastructure\Database\Queries\Select\Term\Query $termQuery,
		protected Infrastructure\Mapper\Tag\Mapper $tagMapper,
	) {
	}

	public function all(): Domain\Tags
	{
		return $this->tagMapper->all(($this->select)($this->termQuery));
	}
}