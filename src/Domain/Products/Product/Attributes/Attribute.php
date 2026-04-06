<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products\Product\Attributes;

use Hoo\WordPressPluginFramework\Collection;

readonly class Attribute implements Collection\Item\ItemInterface
{
	public Attribute\Terms $terms;

	public function __construct(
		protected Attribute\Name $name,
		public bool $isVisible,
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