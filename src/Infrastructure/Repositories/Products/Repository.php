<?php

namespace Hoo\ProductFeeds\Infrastructure\Repositories\Products;

use Hoo\ProductFeeds\Infrastructure;

class Repository
{
	public function __construct(
		protected readonly Infrastructure\Database\Database $database,
		protected readonly Infrastructure\Database\Queries\Select\TermTaxonomy\Excluded\Query $selectExcludedTermTaxonomyQuery,
		protected readonly Infrastructure\Database\Queries\Select\Product\Simple\Query $selectSimpleProductQuery,
		protected readonly Infrastructure\Mappers\TermTaxonomy\Mapper $termTaxonomyMapper,
		protected readonly Infrastructure\Mappers\Products\Mapper $productsMapper,
	) {
	}

	public function __invoke()
	{
		$excludedTermTaxonomies = $this->database->select(
			$this->selectExcludedTermTaxonomyQuery
		);

		$excludedTermTaxonomyIds = $this->termTaxonomyMapper->ids($excludedTermTaxonomies);

		$simpleProducts = $this->database->select(
			$this->selectSimpleProductQuery
				->exclude(...$excludedTermTaxonomyIds)
		);

		return ($this->productsMapper)([
			...$simpleProducts,
		]);
	}
}