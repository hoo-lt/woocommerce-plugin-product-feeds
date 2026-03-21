<?php

namespace Hoo\ProductFeeds\Domain\Terms\Term;

use Hoo\WordPressPluginFramework\Collection;

readonly class Id implements Collection\Item\Key\KeyInterface
{
	public function __construct(
		protected int $id,
	) {
	}

	public function __invoke(): int|string
	{
		return $this->id;
	}
}