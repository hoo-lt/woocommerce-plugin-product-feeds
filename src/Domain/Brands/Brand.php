<?php

namespace Hoo\ProductFeeds\Domain\Brands;

class Brand
{
	public function __construct(
		public int $id,
		public string $name,
		public string $url,
	) {
	}
}