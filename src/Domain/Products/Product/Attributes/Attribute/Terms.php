<?php

namespace Hoo\ProductFeeds\Domain\Products\Product\Attributes\Attribute;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class Terms implements IteratorAggregate
{
	protected array $terms = [];

	public function get(int $id): Terms\Term
	{
		if (!isset($this->terms[$id])) {
			//throw domain exception
		}

		return $this->terms[$id];
	}

	public function has(int $id): bool
	{
		return isset($this->terms[$id]);
	}

	public function add(Terms\Term $term): void
	{
		if (isset($this->terms[$term->id])) {
			return; //throw domain exception
		}

		$this->terms[$term->id] = $term;
	}

	public function delete(int $id): void
	{
		if (isset($this->terms[$id])) {
			return; //throw domain exception
		}

		unset($this->terms[$id]);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(array_values($this->terms));
	}
}