<?php

namespace Hoo\ProductFeeds\Domain\Products\Product;

use Hoo\WordPressPluginFramework\Collection;

class CategoryIds extends Collection\AbstractCollection
{
	public function __construct(
		CategoryIds\CategoryId ...$items,
	) {
		$this->items = $items;
	}

	public function get(Collection\Item\Key\KeyInterface $key): ?CategoryIds\CategoryId
	{
		return parent::get($key);
	}

	public function first(): ?CategoryIds\CategoryId
	{
		return parent::first();
	}

	public function last(): ?CategoryIds\CategoryId
	{
		return parent::last();
	}

	public function add(CategoryIds\CategoryId $item): void
	{
		$key = $item->key();
		if ($this->has($key)) {
			return;
		}

		$this->items[$key()] = $item;
	}
}