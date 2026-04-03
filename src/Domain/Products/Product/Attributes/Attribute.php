<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products\Product\Attributes;

use Hoo\WordPressPluginFramework\Collection;

class Attribute implements Collection\Item\ItemInterface
{
	public readonly Attribute\Terms $terms;

	public function __construct(
		protected readonly Attribute\Name $name,
	) {
		$this->terms = new Attribute\Terms();
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