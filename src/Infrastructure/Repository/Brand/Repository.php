<?php

namespace Hoo\ProductFeeds\Infrastructure\Repository\Brand;

use Hoo\WordPressPluginFramework\Database\DatabaseInterface;
use Hoo\ProductFeeds\Domain;
use Hoo\ProductFeeds\Infrastructure;

class Repository implements Domain\Repository\Brand\RepositoryInterface
{
	public function __construct(
		protected readonly DatabaseInterface $database,
		protected readonly Infrastructure\Database\Query\Select\Term\Query $selectTermQuery,
		protected readonly Infrastructure\Mapper\Brand\Mapper $brandMapper,
	) {
	}

	public function all(): Domain\Brands
	{
		return $this->brandMapper->all($this->database->select($this->selectTermQuery));
	}
}