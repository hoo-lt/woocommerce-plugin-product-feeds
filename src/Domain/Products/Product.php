<?php

namespace Hoo\ProductFeeds\Domain\Products;

class Product
{
	public Product\Brands $brands;
	public Product\Categories $categories;

	public function __construct(
		public int $id,
		public string $name,
		public string $slug,
		public float $price,
		public int $stock,
		public string $gtin,
	) {
		$this->brands = new Product\Brands();
		$this->categories = new Product\Categories();
	}
}