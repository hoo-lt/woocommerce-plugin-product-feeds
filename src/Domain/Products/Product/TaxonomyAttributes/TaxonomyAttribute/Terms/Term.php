<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products\Product\TaxonomyAttributes\TaxonomyAttribute\Terms;

use Hoo\WordPressPluginFramework\Collection;

class Term implements Collection\Item\ItemInterface
{
	public function __construct(
		protected readonly Term\Slug $slug,
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