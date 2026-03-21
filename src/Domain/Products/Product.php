<?php

namespace Hoo\ProductFeeds\Domain\Products;

use Hoo\WordPressPluginFramework\Collection;
use Hoo\WordPressPluginFramework\Http;

class Product implements Collection\Item\ItemInterface
{
	public Product\Attributes $attributes;
	public Product\AttributeSlugs $attributeSlugs;
	public Product\BrandIds $brandIds;
	public Product\CategoryIds $categoryIds;
	public Product\TagIds $tagIds;

	public function __construct(
		protected readonly Product\Id $id,
		public string $name,
		public Http\UrlInterface $url,
		public float $price,
		public ?int $stock,
		public ?string $gtin,
	) {
		$this->attributes = new Product\Attributes();
		$this->attributeSlugs = new Product\AttributeSlugs();
		$this->brandIds = new Product\BrandIds();
		$this->categoryIds = new Product\CategoryIds();
		$this->tagIds = new Product\TagIds();
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