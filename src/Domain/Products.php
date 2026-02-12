<?php

namespace Hoo\ProductFeeds\Domain;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class Products implements IteratorAggregate
{
	protected array $products = [];

	public function has(int $id): bool
	{
		return isset($this->products[$id]);
	}

	public function get(int $id): Products\Product
	{
		if (!isset($this->products[$id])) {
			//throw domain exception
		}

		return $this->products[$id];
	}

	public function first(): Products\Product
	{
		if (!$this->products) {
			//throw exception
		}

		$firstKey = array_key_first($this->products);
		return $this->products[$firstKey];
	}

	public function last(): Products\Product
	{
		if (!$this->products) {
			//throw exception
		}

		$lastKey = array_key_last($this->products);
		return $this->products[$lastKey];
	}

	public function add(Products\Product $product): void
	{
		if (isset($this->products[$product->id])) {
			return; //throw domain exception
		}

		$this->products[$product->id] = $product;
	}

	public function remove(int $id): void
	{
		if (!isset($this->products[$id])) {
			return; //throw domain exception
		}

		unset($this->products[$id]);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(array_values($this->products));
	}
}