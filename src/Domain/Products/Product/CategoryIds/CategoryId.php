<?php

namespace Hoo\ProductFeeds\Domain\Products\Product\CategoryIds;

use Hoo\WordPressPluginFramework\Collection;

class CategoryId implements Collection\Item\ItemInterface, Collection\Item\Key\KeyInterface
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