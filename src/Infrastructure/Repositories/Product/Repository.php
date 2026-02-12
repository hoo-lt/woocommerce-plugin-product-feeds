<?php

namespace Hoo\ProductFeeds\Infrastructure\Repositories\Product;

use Hoo\ProductFeeds\Domain;
use Hoo\ProductFeeds\Infrastructure;

class Repository implements Domain\Repositories\Product\RepositoryInterface
{
	public function __construct(
		protected readonly Infrastructure\Database\DatabaseInterface $database,
		protected readonly Infrastructure\Database\Queries\Select\TermTaxonomy\Excluded\Query $selectExcludedTermTaxonomyQuery,
		protected readonly Infrastructure\Database\Queries\Select\Product\Simple\Query $selectSimpleProductQuery,
		protected readonly Infrastructure\Mappers\TermTaxonomy\Mapper $termTaxonomyMapper,
		protected readonly Infrastructure\Mappers\Product\Mapper $productMapper,
	) {
	}

	public function __invoke(): Domain\Products
	{
		$excludedTermTaxonomyIds = ($this->termTaxonomyMapper)($this->database->select(
			$this->selectExcludedTermTaxonomyQuery
		));

		return ($this->productMapper)([
			...$this->database->select(
				$this->selectSimpleProductQuery
					->exclude(...$excludedTermTaxonomyIds)
			),
		]);
	}
}