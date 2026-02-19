<?php

namespace Hoo\ProductFeeds\Domain\Categories;

class Category
{
	public function __construct(
		public int $id,
		public string $name,
		public string $url,
	) {
	}
}