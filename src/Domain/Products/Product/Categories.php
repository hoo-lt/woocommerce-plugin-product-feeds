<?php

namespace Hoo\ProductFeeds\Domain\Products\Product;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class Categories implements IteratorAggregate
{
	protected array $categories = [];

	public function get(int $id): Categories\Category
	{
		if (!isset($this->categories[$id])) {
			//throw domain exception
		}

		return $this->categories[$id];
	}

	public function has(int $id): bool
	{
		return isset($this->categories[$id]);
	}

	public function add(Categories\Category $category): void
	{
		if (isset($this->categories[$category->id])) {
			return; //throw domain exception
		}

		$this->categories[$category->id] = $category;
	}

	public function delete(int $id): void
	{
		if (!isset($this->categories[$id])) {
			return; //throw domain exception
		}

		unset($this->categories[$id]);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(array_values($this->categories));
	}
}