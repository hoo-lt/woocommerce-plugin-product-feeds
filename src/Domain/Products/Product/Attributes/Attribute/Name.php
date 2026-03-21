<?php

namespace Hoo\ProductFeeds\Domain\Products\Product\Attributes\Attribute;

use Hoo\WordPressPluginFramework\Collection;

readonly class Name implements Collection\Item\Key\KeyInterface
{
	public function __construct(
		protected string $name,
	) {
	}

	public function __invoke(): string
	{
		return $this->name;
	}
}