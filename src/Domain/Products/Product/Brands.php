<?php

namespace Hoo\ProductFeeds\Domain\Products\Product;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class Brands implements IteratorAggregate
{
	protected array $brands = [];

	public function has(int $id): bool
	{
		return isset($this->brands[$id]);
	}

	public function get(int $id): Brands\Brand
	{
		if (!isset($this->brands[$id])) {
			//throw domain exception
		}

		return $this->brands[$id];
	}

	public function first(): Brands\Brand
	{
		if (!$this->brands) {
			//throw exception
		}

		$firstKey = array_key_first($this->brands);
		return $this->brands[$firstKey];
	}

	public function last(): Brands\Brand
	{
		if (!$this->brands) {
			//throw exception
		}

		$lastKey = array_key_last($this->brands);
		return $this->brands[$lastKey];
	}

	public function add(Brands\Brand $brand): void
	{
		if (isset($this->brands[$brand->id])) {
			return; //throw domain exception
		}

		$this->brands[$brand->id] = $brand;
	}

	public function remove(int $id): void
	{
		if (isset($this->brands[$id])) {
			return; //throw domain exception
		}

		unset($this->brands[$id]);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(array_values($this->brands));
	}
}