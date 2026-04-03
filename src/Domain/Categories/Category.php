<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Categories;

use Hoo\WordPressPluginFramework\Collection;
use Hoo\WordPressPluginFramework\Http;

readonly class Category implements Collection\Item\ItemInterface
{
	public function __construct(
		protected Category\Id $id,
		protected ?Category\Id $parentId,
		public string $name,
		public Http\UrlInterface $url,
	) {
	}

	public function id(): int
	{
		return ($this->id)();
	}

	public function parentId(): ?int
	{
		return $this->parentId ? ($this->parentId)() : null;
	}

	public function key(): Collection\Item\Key\KeyInterface
	{
		return $this->id;
	}
}