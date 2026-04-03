<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products;

use Hoo\WordPressPluginFramework\Collection;
use Hoo\WordPressPluginFramework\Http;

readonly class Product implements Collection\Item\ItemInterface
{
	public Product\Attributes $attributes;
	public Product\TaxonomyAttributes $taxonomyAttributes;
	public Product\BrandIds $brandIds;
	public Product\CategoryIds $categoryIds;
	public Product\TagIds $tagIds;

	public function __construct(
		protected Product\Id $id,
		protected ?Product\Id $parentId,
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