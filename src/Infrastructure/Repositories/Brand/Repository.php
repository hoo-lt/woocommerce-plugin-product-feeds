<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Repositories\Brand;

use Hoo\WordPressPluginFramework\Database\SelectInterface;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure;

readonly class Repository implements Domain\Repository\Brand\RepositoryInterface
{
	public function __construct(
		protected SelectInterface $select,
		protected Infrastructure\Database\Queries\Select\Term\Query $termQuery,
		protected Infrastructure\Mappers\Brand\Mapper $brandMapper,
	) {
	}

	public function all(): Domain\Brands
	{
		return $this->brandMapper->map(($this->select)($this->termQuery));
	}
}