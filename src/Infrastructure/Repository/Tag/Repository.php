<?php

namespace Hoo\ProductFeeds\Infrastructure\Repository\Tag;

use Hoo\WordPressPluginFramework\Database\DatabaseInterface;
use Hoo\ProductFeeds\Domain;
use Hoo\ProductFeeds\Infrastructure;

class Repository implements Domain\Repository\Tag\RepositoryInterface
{
	public function __construct(
		protected readonly DatabaseInterface $database,
		protected readonly Infrastructure\Database\Query\Select\Term\Query $selectTermQuery,
		protected readonly Infrastructure\Mapper\Tag\Mapper $tagMapper,
	) {
	}

	public function all(): Domain\Tags
	{
		return $this->tagMapper->all($this->database->select($this->selectTermQuery));
	}
}