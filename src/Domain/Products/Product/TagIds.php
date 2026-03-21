<?php

namespace Hoo\ProductFeeds\Domain\Products\Product;

use Hoo\WordPressPluginFramework\Collection;

class TagIds extends Collection\AbstractCollection
{
	public function __construct(
		TagIds\TagId ...$items,
	) {
		$this->items = $items;
	}

	public function get(Collection\Item\Key\KeyInterface $key): ?TagIds\TagId
	{
		return parent::get($key);
	}

	public function first(): ?TagIds\TagId
	{
		return parent::first();
	}

	public function last(): ?TagIds\TagId
	{
		return parent::last();
	}

	public function add(TagIds\TagId $item): void
	{
		$key = $item->key();
		if ($this->has($key)) {
			return;
		}

		$this->items[$key()] = $item;
	}
}