<?php

namespace Hoo\ProductFeeds\Domain;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

class Tags implements IteratorAggregate
{
	protected array $tags = [];

	public function has(int $id): bool
	{
		return isset($this->tags[$id]);
	}

	public function get(int $id): Tags\Tag
	{
		if (!isset($this->tags[$id])) {
			//throw domain exception
		}

		return $this->tags[$id];
	}

	public function first(): ?Tags\Tag
	{
		if (!$this->tags) {
			return null;
		}

		$firstKey = array_key_first($this->tags);
		return $this->tags[$firstKey];
	}

	public function last(): ?Tags\Tag
	{
		if (!$this->tags) {
			return null;
		}

		$lastKey = array_key_last($this->tags);
		return $this->tags[$lastKey];
	}

	public function add(Tags\Tag $category): void
	{
		if (isset($this->tags[$category->id])) {
			return; //throw domain exception
		}

		$this->tags[$category->id] = $category;
	}

	public function remove(int $id): void
	{
		if (!isset($this->tags[$id])) {
			return; //throw domain exception
		}

		unset($this->tags[$id]);
	}

	public function all(): array
	{
		return array_values($this->tags);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(array_values($this->tags));
	}
}