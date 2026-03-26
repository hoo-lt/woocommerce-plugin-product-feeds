<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Repository\Attribute;

use Hoo\WordPressPluginFramework\Database\DatabaseInterface;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure;

readonly class Repository implements Domain\Repository\Attribute\RepositoryInterface
{
	public function __construct(
		protected DatabaseInterface $database,
		protected Infrastructure\Database\Query\Select\Attribute\Query $selectAttributeQuery,
		protected Infrastructure\Mapper\Attribute\Mapper $attributeMapper,
	) {
	}

	public function all(): Domain\Attributes
	{
		return $this->attributeMapper->all($this->database->json($this->selectAttributeQuery));
	}
}