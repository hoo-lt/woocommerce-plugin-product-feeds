<?php

namespace Hoo\ProductFeeds\Domain\Products\Product\AttributeSlugs\AttributeSlug\TermIds;

use Hoo\WordPressPluginFramework\Collection;

class TermId implements Collection\Item\ItemInterface, Collection\Item\Key\KeyInterface
{
	public function __construct(
		protected int $id,
	) {
	}

	public function __invoke(): int
	{
		return $this->id;
	}

	public function key(): Collection\Item\Key\KeyInterface
	{
		return $this;
	}
}