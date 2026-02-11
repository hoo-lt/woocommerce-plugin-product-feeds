<?php

namespace Hoo\ProductFeeds\Application\Dtos\Products;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

readonly class Products implements IteratorAggregate
{
	protected array $products;

	public function __construct(
		Product\Product ...$products
	) {
		$this->products = $products;
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->products);
	}
}