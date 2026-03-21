<?php

namespace Hoo\ProductFeeds\Domain\Products\Product\Attributes\Attribute;

use Hoo\WordPressPluginFramework\Collection;

class Terms extends Collection\AbstractCollection
{
	public function __construct(
		Terms\Term ...$items,
	) {
		$this->items = $items;
	}

	public function get(Collection\Item\Key\KeyInterface $key): ?Terms\Term
	{
		return parent::get($key);
	}

	public function first(): ?Terms\Term
	{
		return parent::first();
	}

	public function last(): ?Terms\Term
	{
		return parent::last();
	}

	public function add(Terms\Term $item): void
	{
		$key = $item->key();
		if ($this->has($key)) {
			return;
		}

		$this->items[$key()] = $item;
	}
}