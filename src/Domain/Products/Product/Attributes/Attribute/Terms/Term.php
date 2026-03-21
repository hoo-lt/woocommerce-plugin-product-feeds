<?php

namespace Hoo\ProductFeeds\Domain\Products\Product\Attributes\Attribute\Terms;

use Hoo\WordPressPluginFramework\Collection;

class Term implements Collection\Item\ItemInterface
{
	public function __construct(
		protected readonly Term\Name $name,
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