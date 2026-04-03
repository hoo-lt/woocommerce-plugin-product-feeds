<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products\Product\TaxonomyAttributes;

use Hoo\WordPressPluginFramework\Collection;

readonly class TaxonomyAttribute implements Collection\Item\ItemInterface
{
	public TaxonomyAttribute\Terms $terms;

	public function __construct(
		protected TaxonomyAttribute\Slug $slug,
		public bool $isVisible,
		public bool $isVariation,
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