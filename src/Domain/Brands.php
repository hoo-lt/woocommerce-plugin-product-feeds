<?php

namespace Hoo\ProductFeeds\Domain;

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

	public function first(): ?Brands\Brand
	{
		if (!$this->brands) {
			return null;
		}

		$firstKey = array_key_first($this->brands);
		return $this->brands[$firstKey];
	}

	public function last(): ?Brands\Brand
	{
		if (!$this->brands) {
			return null;
		}

		$lastKey = array_key_last($this->brands);
		return $this->brands[$lastKey];
	}

	public function add(Brands\Brand $category): void
	{
		if (isset($this->brands[$category->id])) {
			return; //throw domain exception
		}

		$this->brands[$category->id] = $category;
	}

	public function remove(int $id): void
	{
		if (!isset($this->brands[$id])) {
			return; //throw domain exception
		}

		unset($this->brands[$id]);
	}

	public function all(): array
	{
		return array_values($this->brands);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(array_values($this->brands));
	}
}