<?php

namespace Hoo\ProductFeeds\Domain\Products\Product\Attributes;

class Attribute
{
	public Attribute\Terms $terms;

	public function __construct(
		public int $id,
		public string $name,
		public string $slug,
	) {
		$this->terms = new Attribute\Terms();
	}
}