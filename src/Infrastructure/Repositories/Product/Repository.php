<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Repositories\Product;

use Hoo\WordPressPluginFramework\Database\DatabaseInterface;
use Hoo\WordPressPluginFramework\Json\JsonInterface;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure;

readonly class Repository implements Domain\Repository\Product\RepositoryInterface
{
	public function __construct(
		protected DatabaseInterface $database,
		protected JsonInterface $json,
		protected Infrastructure\Database\Queries\Select\Product\Simple\Query $selectSimpleProductQuery,
		protected Infrastructure\Mapper\Product\Simple\Mapper $simpleProductMapper,
		protected Infrastructure\Database\Queries\Select\Product\Variation\Query $selectProductVariationQuery,
		protected Infrastructure\Mapper\Product\Variation\Mapper $productVariationMapper,
		protected array $ids = [],
		protected array $statuses = [],
	) {
	}

	public function withIds(int ...$ids): self
	{
		return new self(
			$this->database,
			$this->selectSimpleProductQuery,
			$this->simpleProductMapper,
			$this->selectProductVariationQuery,
			$this->productVariationMapper,
			$ids,
			$this->statuses
		);
	}

	public function withStatuses(Domain\Products\Product\Status ...$statuses): self
	{
		return new self(
			$this->database,
			$this->selectSimpleProductQuery,
			$this->simpleProductMapper,
			$this->selectProductVariationQuery,
			$this->productVariationMapper,
			$this->ids,
			$statuses
		);
	}

	public function all(): Domain\Products
	{
		$selectSimpleProductQuery = $this->selectSimpleProductQuery;
		$selectProductVariationQuery = $this->selectProductVariationQuery;

		if ($this->ids) {
			$selectSimpleProductQuery = $selectSimpleProductQuery
				->withIds(...$this->ids);
			$selectProductVariationQuery = $selectProductVariationQuery
				->withIds(...$this->ids);
		}

		if ($this->statuses) {
			$selectSimpleProductQuery = $selectSimpleProductQuery
				->withStatuses(...$this->statuses);
			$selectProductVariationQuery = $selectProductVariationQuery
				->withStatuses(...$this->statuses)
				->withParentStatuses(...$this->statuses);
		}

		$products = new Domain\Products();
		$products->merge($this->simpleProductMapper->all($this->database->select($selectSimpleProductQuery)));
		$products->merge($this->productVariationMapper->all($this->database->select($selectProductVariationQuery)));

		return $products;
	}
}