<?php

namespace Hoo\ProductFeeds\Domain\Products\Product;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class Attributes implements IteratorAggregate
{
	protected array $attributes = [];

	public function has(int $id): bool
	{
		return isset($this->attributes[$id]);
	}

	public function get(int $id): Attributes\Attribute
	{
		if (!isset($this->attributes[$id])) {
			//throw domain exception
		}

		return $this->attributes[$id];
	}

	public function first(): Attributes\Attribute
	{
		if (!$this->attributes) {
			//throw exception
		}

		$firstKey = array_key_first($this->attributes);
		return $this->attributes[$firstKey];
	}

	public function last(): Attributes\Attribute
	{
		if (!$this->attributes) {
			//throw exception
		}

		$lastKey = array_key_last($this->attributes);
		return $this->attributes[$lastKey];
	}

	public function add(Attributes\Attribute $attribute): void
	{
		if (isset($this->attributes[$attribute->id])) {
			return; //throw domain exception
		}

		$this->attributes[$attribute->id] = $attribute;
	}

	public function remove(int $id): void
	{
		if (isset($this->attributes[$id])) {
			return; //throw domain exception
		}

		unset($this->attributes[$id]);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(array_values($this->attributes));
	}
}