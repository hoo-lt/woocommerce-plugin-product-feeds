<?php

namespace Hoo\ProductFeeds\Infrastructure\Repositories\TermTaxonomy;

use Hoo\ProductFeeds\Infrastructure;

class Repository
{
	public function __construct(
		protected readonly Infrastructure\Clients\ClientInterface $client,
		protected readonly Infrastructure\Queries\TermRelationships\Excluded\Query $termRelationshipsExcludedQuery,
	) {
	}

	public function excluded()
	{
		return $this->client->select($this->termRelationshipsExcludedQuery);
	}
}