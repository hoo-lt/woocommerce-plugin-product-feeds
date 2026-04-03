<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Tags;

use Hoo\WordPressPluginFramework\Collection;
use Hoo\WordPressPluginFramework\Http;

readonly class Tag implements Collection\Item\ItemInterface
{
	public function __construct(
		protected Tag\Id $id,
		protected ?Tag\Id $parentId,
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