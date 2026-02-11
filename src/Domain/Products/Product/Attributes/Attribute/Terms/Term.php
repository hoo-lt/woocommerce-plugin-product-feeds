<?php

namespace Hoo\ProductFeeds\Domain\Products\Product\Attributes\Attribute\Terms;

class Term
{
	public function __construct(
		public int $id,
		public string $name,
		public string $slug,
	) {
	}
}