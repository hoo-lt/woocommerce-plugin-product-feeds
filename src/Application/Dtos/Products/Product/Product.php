<?php

namespace Hoo\ProductFeeds\Application\Dtos\Products\Product;

readonly class Product
{
	public function __construct(
		public int $id,
		public string $name,
		public float $price,
		public int $stock,
		public string $gtin,
	) {

	}
}