<?php

namespace Hoo\ProductFeeds\Domain\Products\Product\AttributeSlugs;

use Hoo\WordPressPluginFramework\Collection;

class AttributeSlug implements Collection\Item\ItemInterface, Collection\Item\Key\KeyInterface
{
	public AttributeSlug\TermIds $termIds;

	public function __construct(
		protected string $slug,
	) {
		$this->termIds = new AttributeSlug\TermIds();
	}

	public function __invoke(): string
	{
		return $this->slug;
	}

	public function key(): Collection\Item\Key\KeyInterface
	{
		return $this;
	}
}