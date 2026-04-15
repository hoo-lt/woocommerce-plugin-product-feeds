<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Repositories\TermRelationship;

use Hoo\WordPressPluginFramework\Database\SelectInterface;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure;

readonly class Repository implements Domain\Repository\TermRelationship\RepositoryInterface
{
	public function __construct(
		protected SelectInterface $select,
		protected Infrastructure\Database\Queries\Select\TermRelationship\Query $termRelationshipQuery,
		protected Infrastructure\Mapper\TermRelationship\Mapper $termRelationshipMapper,
	) {
	}

	public function objectIds(): array
	{
		return $this->termRelationshipMapper->objectIds(($this->select)($this->termRelationshipQuery));
	}
}