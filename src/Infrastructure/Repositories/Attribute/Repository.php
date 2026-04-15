<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Repositories\Attribute;

use Hoo\WordPressPluginFramework\Database\SelectInterface;
use Hoo\WordPressPluginFramework\Json\JsonInterface;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure;

readonly class Repository implements Domain\Repository\Attribute\RepositoryInterface
{
	public function __construct(
		protected SelectInterface $select,
		protected JsonInterface $json,
		protected Infrastructure\Database\Queries\Select\Attribute\Query $attributeQuery,
		protected Infrastructure\Mapper\Attribute\Mapper $attributeMapper,
	) {
	}

	public function all(): Domain\Attributes
	{
		return $this->attributeMapper->map(
			$this->json->decode(
				($this->select)($this->attributeQuery)[0]['attributes'],
			),
		);
	}
}