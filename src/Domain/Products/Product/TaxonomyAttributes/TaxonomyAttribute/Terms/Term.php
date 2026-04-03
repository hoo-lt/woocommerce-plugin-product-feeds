<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products\Product\TaxonomyAttributes\TaxonomyAttribute\Terms;

use Hoo\WordPressPluginFramework\Collection;

readonly class Term implements Collection\Item\ItemInterface
{
	public function __construct(
		protected Term\Slug $slug,
	) {
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