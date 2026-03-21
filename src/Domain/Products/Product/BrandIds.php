<?php

namespace Hoo\ProductFeeds\Domain\Products\Product;

use Hoo\WordPressPluginFramework\Collection;

class BrandIds extends Collection\AbstractCollection
{
	public function __construct(
		BrandIds\BrandId ...$items,
	) {
		$this->items = $items;
	}

	public function get(Collection\Item\Key\KeyInterface $key): ?BrandIds\BrandId
	{
		return parent::get($key);
	}

	public function first(): ?BrandIds\BrandId
	{
		return parent::first();
	}

	public function last(): ?BrandIds\BrandId
	{
		return parent::last();
	}

	public function add(BrandIds\BrandId $item): void
	{
		$key = $item->key();
		if ($this->has($key)) {
			return;
		}

		$this->items[$key()] = $item;
	}
}