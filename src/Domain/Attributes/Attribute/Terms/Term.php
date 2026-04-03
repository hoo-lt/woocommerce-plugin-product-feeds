<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Attributes\Attribute\Terms;

use Hoo\WordPressPluginFramework\Collection;

readonly class Term implements Collection\Item\ItemInterface
{
	public function __construct(
		protected Term\Slug $slug,
		public string $name,
	) {
	}

	public function id(): string
	{
		return ($this->slug)();
	}

	public function key(): Collection\Item\Key\KeyInterface
	{
		return $this->slug;
	}
}