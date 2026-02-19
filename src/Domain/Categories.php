<?php

namespace Hoo\ProductFeeds\Domain;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class Categories implements IteratorAggregate
{
	protected array $categories = [];

	public function has(int $id): bool
	{
		return isset($this->categories[$id]);
	}

	public function get(int $id): Categories\Category
	{
		if (!isset($this->categories[$id])) {
			//throw domain exception
		}

		return $this->categories[$id];
	}

	public function first(): ?Categories\Category
	{
		if (!$this->categories) {
			return null;
		}

		$firstKey = array_key_first($this->categories);
		return $this->categories[$firstKey];
	}

	public function last(): ?Categories\Category
	{
		if (!$this->categories) {
			return null;
		}

		$lastKey = array_key_last($this->categories);
		return $this->categories[$lastKey];
	}

	public function add(Categories\Category $category): void
	{
		if (isset($this->categories[$category->id])) {
			return; //throw domain exception
		}

		$this->categories[$category->id] = $category;
	}

	public function remove(int $id): void
	{
		if (!isset($this->categories[$id])) {
			return; //throw domain exception
		}

		unset($this->categories[$id]);
	}

	public function all(): array
	{
		return array_values($this->categories);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(array_values($this->categories));
	}
}