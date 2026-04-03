<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products\Product\Attributes\Attribute\Terms;

use Hoo\WordPressPluginFramework\Collection;

readonly class Term implements Collection\Item\ItemInterface
{
	public function __construct(
		protected Term\Name $name,
	) {
	}

	public function name(): string
	{
		return ($this->name)();
	}

	public function key(): Collection\Item\Key\KeyInterface
	{
		return $this->name;
	}
}