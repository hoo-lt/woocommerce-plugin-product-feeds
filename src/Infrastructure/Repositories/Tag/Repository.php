<?php

namespace Hoo\ProductFeeds\Infrastructure\Repositories\Tag;

use Hoo\ProductFeeds\Domain;
use Hoo\ProductFeeds\Infrastructure;

class Repository
{
	public function __construct(
		protected readonly Infrastructure\Database\DatabaseInterface $database,
		protected readonly Infrastructure\Database\Queries\Select\Tag\Query $selectTagQuery,
		protected readonly Infrastructure\Mappers\Tag\Mapper $tagMapper,
	) {
	}

	public function all(): Domain\Tags
	{
		return $this->tagMapper->all($this->database->select($this->selectTagQuery));
	}
}