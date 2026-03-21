<?php

namespace Hoo\ProductFeeds\Domain\Products\Product\AttributeSlugs\AttributeSlug;

use Hoo\WordPressPluginFramework\Collection;

class TermIds extends Collection\AbstractCollection
{
	public function __construct(
		TermIds\TermId ...$items,
	) {
		$this->items = $items;
	}

	public function get(Collection\Item\Key\KeyInterface $key): ?TermIds\TermId
	{
		return parent::get($key);
	}

	public function first(): ?TermIds\TermId
	{
		return parent::first();
	}

	public function last(): ?TermIds\TermId
	{
		return parent::last();
	}

	public function add(TermIds\TermId $item): void
	{
		$key = $item->key();
		if ($this->has($key)) {
			return;
		}

		$this->items[$key()] = $item;
	}
}