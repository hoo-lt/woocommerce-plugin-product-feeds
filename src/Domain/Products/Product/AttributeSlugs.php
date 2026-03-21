<?php

namespace Hoo\ProductFeeds\Domain\Products\Product;

use Hoo\WordPressPluginFramework\Collection;

class AttributeSlugs extends Collection\AbstractCollection
{
	public function __construct(
		AttributeSlugs\AttributeSlug ...$items,
	) {
		$this->items = $items;
	}

	public function get(Collection\Item\Key\KeyInterface $key): ?AttributeSlugs\AttributeSlug
	{
		return parent::get($key);
	}

	public function first(): ?AttributeSlugs\AttributeSlug
	{
		return parent::first();
	}

	public function last(): ?AttributeSlugs\AttributeSlug
	{
		return parent::last();
	}

	public function add(AttributeSlugs\AttributeSlug $item): void
	{
		$key = $item->key();
		if ($this->has($key)) {
			return;
		}

		$this->items[$key()] = $item;
	}
}