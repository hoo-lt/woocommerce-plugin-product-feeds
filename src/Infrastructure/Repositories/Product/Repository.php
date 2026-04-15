<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Repositories\Product;

use Hoo\WordPressPluginFramework\Database\SelectInterface;
use Hoo\WordPressPluginFramework\Json\JsonInterface;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure;

readonly class Repository implements Domain\Repository\Product\RepositoryInterface
{
	public function __construct(
		protected SelectInterface $select,
		protected JsonInterface $json,
		protected Infrastructure\Database\Queries\Select\Product\Simple\Query $simpleProductQuery,
		protected Infrastructure\Mappers\Product\Simple\Mapper $simpleProductMapper,
		protected Infrastructure\Database\Queries\Select\Product\Variation\Query $productVariationQuery,
		protected Infrastructure\Mappers\Product\Variation\Mapper $productVariationMapper,
		protected array $ids = [],
		protected array $statuses = [],
	) {
	}

	public function withIds(int ...$ids): self
	{
		return new self(
			$this->select,
			$this->json,
			$this->simpleProductQuery,
			$this->simpleProductMapper,
			$this->productVariationQuery,
			$this->productVariationMapper,
			$ids,
			$this->statuses
		);
	}

	public function withStatuses(Domain\Products\Product\Status ...$statuses): self
	{
		return new self(
			$this->select,
			$this->json,
			$this->simpleProductQuery,
			$this->simpleProductMapper,
			$this->productVariationQuery,
			$this->productVariationMapper,
			$this->ids,
			$statuses
		);
	}

	public function all(): Domain\Products
	{
		$simpleProductQuery = $this->simpleProductQuery;
		$productVariationQuery = $this->productVariationQuery;

		if ($this->ids) {
			$simpleProductQuery = $simpleProductQuery
				->withIds(...$this->ids);
			$productVariationQuery = $productVariationQuery
				->withIds(...$this->ids);
		}

		if ($this->statuses) {
			$simpleProductQuery = $simpleProductQuery
				->withStatuses(...$this->statuses);
			$productVariationQuery = $productVariationQuery
				->withStatuses(...$this->statuses)
				->withParentStatuses(...$this->statuses);
		}

		$products = new Domain\Products();
		$products->merge($this->simpleProductMapper->map(
			$this->json->decode(
				($this->select)($simpleProductQuery)[0]['products']
			)
		));
		$products->merge($this->productVariationMapper->map(
			$this->json->decode(
				($this->select)($productVariationQuery)[0]['products']
			)
		));

		return $products;
	}
}