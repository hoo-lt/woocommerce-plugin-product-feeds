<?php

namespace Hoo\ProductFeeds\Domain\Products\Product\Brands;

class Brand
{
	public function __construct(
		public int $id,
		public string $name,
		public string $slug,
	) {
	}
}