<?php

namespace Hoo\ProductFeeds\Domain\Terms;

use Hoo\WordPressPluginFramework\Collection;

class Term implements Collection\Item\ItemInterface
{
	public function __construct(
		protected readonly Term\Id $id,
		public string $name,
	) {
	}

	public function id(): int
	{
		return ($this->id)();
	}

	public function key(): Collection\Item\Key\KeyInterface
	{
		return $this->id;
	}
}