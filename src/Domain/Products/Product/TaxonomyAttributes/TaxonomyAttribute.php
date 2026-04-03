<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products\Product\TaxonomyAttributes;

use Hoo\WordPressPluginFramework\Collection;

class TaxonomyAttribute implements Collection\Item\ItemInterface
{
	public readonly TaxonomyAttribute\Terms $terms;

	public function __construct(
		protected readonly TaxonomyAttribute\Slug $slug,
	) {
		$this->terms = new TaxonomyAttribute\Terms();
	}

	public function slug(): string
	{
		return ($this->slug)();
	}

	public function key(): Collection\Item\Key\KeyInterface
	{
		return $this->slug;
	}
}