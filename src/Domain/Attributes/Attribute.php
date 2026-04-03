<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Attributes;

use Hoo\WordPressPluginFramework\Collection;

readonly class Attribute implements Collection\Item\ItemInterface
{
	public Attribute\Terms $terms;

	public function __construct(
		protected Attribute\Slug $slug,
		public string $name,
	) {
		$this->terms = new Attribute\Terms();
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