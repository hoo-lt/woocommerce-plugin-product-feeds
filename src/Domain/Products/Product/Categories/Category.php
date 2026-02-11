<?php

namespace Hoo\ProductFeeds\Domain\Products\Product\Categories;

class Category
{
	public function __construct(
		public int $id,
		public string $name,
		public string $slug,
	) {
	}
}