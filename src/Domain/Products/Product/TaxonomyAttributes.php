<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products\Product;

use Hoo\WordPressPluginFramework\Collection;

class TaxonomyAttributes extends Collection\AbstractCollection
{
	public function __construct(
		TaxonomyAttributes\TaxonomyAttribute ...$items,
	) {
		$this->items = $items;
	}

	public function get(Collection\Item\Key\KeyInterface $key): ?TaxonomyAttributes\TaxonomyAttribute
	{
		return parent::get($key);
	}

	public function first(): ?TaxonomyAttributes\TaxonomyAttribute
	{
		return parent::first();
	}

	public function last(): ?TaxonomyAttributes\TaxonomyAttribute
	{
		return parent::last();
	}

	public function add(TaxonomyAttributes\TaxonomyAttribute $item): void
	{
		$key = $item->key();
		if ($this->has($key)) {
			return;
		}

		$this->items[$key()] = $item;
	}
}