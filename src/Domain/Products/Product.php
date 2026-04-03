<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products;

use Hoo\WordPressPluginFramework\Collection;
use Hoo\WordPressPluginFramework\Http;

class Product implements Collection\Item\ItemInterface
{
	public readonly Product\Attributes $attributes;
	public readonly Product\TaxonomyAttributes $taxonomyAttributes;
	public readonly Product\BrandIds $brandIds;
	public readonly Product\CategoryIds $categoryIds;
	public readonly Product\TagIds $tagIds;

	public function __construct(
		protected readonly Product\Id $id,
		protected readonly ?Product\Id $parentId,
		public string $name,
		public Http\UrlInterface $url,
		public Product\Price $price,
		public ?int $stock,
		public Product\StockStatus $stockStatus,
		public ?string $gtin,
	) {
		$this->attributes = new Product\Attributes();
		$this->taxonomyAttributes = new Product\TaxonomyAttributes();
		$this->brandIds = new Product\BrandIds();
		$this->categoryIds = new Product\CategoryIds();
		$this->tagIds = new Product\TagIds();
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