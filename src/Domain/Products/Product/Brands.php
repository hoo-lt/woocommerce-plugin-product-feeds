<?php

namespace Hoo\ProductFeeds\Domain\Products\Product;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class Brands implements IteratorAggregate
{
	protected array $brands;

	public function get(int $id): Brands\Brand
	{
		if (!$this->brands[$id]) {
			//throw domain exception
		}

		return $this->brands[$id];
	}

	public function has(int $id): bool
	{
		return (bool) $this->brands[$id];
	}

	public function add(Brands\Brand $brand): void
	{
		if ($this->brands[$brand->id]) {
			return; //throw domain exception
		}

		$this->brands[$brand->id] = $brand;
	}

	public function delete(int $id): void
	{
		if (!$this->brands[$id]) {
			return; //throw domain exception
		}

		unset($this->brands[$id]);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->brands);
	}
}