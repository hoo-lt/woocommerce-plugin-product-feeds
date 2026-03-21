<?php

namespace Hoo\ProductFeeds\Domain\Products\Product\BrandIds;

use Hoo\WordPressPluginFramework\Collection;

readonly class BrandId implements Collection\Item\ItemInterface, Collection\Item\Key\KeyInterface
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