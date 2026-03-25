<?php

namespace Hoo\ProductFeeds\Infrastructure\Mapper\Product;

use Hoo\WordPressPluginFramework\Http;
use Hoo\ProductFeeds\Domain;

class Mapper
{
	protected readonly Http\Url $url;

	public function __construct(
		string $url,
		string $path,
	) {
		$this->url = Http\Url::from($url)
			->withPath($path);
	}

	public function all(array $table): Domain\Products
	{
		$products = new Domain\Products();

		foreach ($table as [
			'id' => $id,
			'parent_id' => $parentId,
			'name' => $name,
			'path' => $path,
			'price' => $price,
			'stock' => $stock,
			'global_unique_id' => $globalUniqueId,
			'product_attributes' => $productAttributes,
			'attribute_slug' => $attributeSlug,
			'term_slug' => $termSlug,
			'brand_id' => $brandId,
			'category_id' => $categoryId,
			'tag_id' => $tagId,
		]) {
			$id = new Domain\Products\Product\Id(
				$id,
			);
			$parentId = new Domain\Products\Product\Id(
				$parentId,
			);

			if ($products->has($id)) {
				$product = $products->get($id);
			} else {
				$product = new Domain\Products\Product(
					$id,
					$parentId,
					$name,
					$this->url->withPath("{$this->url->path()}/{$path}"),
					$price,
					$stock,
					$globalUniqueId,
				);
				$products->add($product);
			}

			/*
			if ($productAttributes) {
				foreach (unserialize($productAttributes) as [
					'name' => $name,
					'value' => $value,
					'is_taxonomy' => $isTaxonomy,
				]) {
					if ($isTaxonomy) {
						continue;
					}

					$attributeName = new Domain\Products\Product\Attributes\Attribute\Name(
						$name,
					);

					if ($product->attributes->has($attributeName)) {
						$attribute = $product->attributes->get($attributeName);
					} else {
						$attribute = new Domain\Products\Product\Attributes\Attribute(
							$attributeName
						);

						$product->attributes->add($attribute);
					}

					if ($value) {
						foreach (explode('|', $value) as $name) {
							$termName = new Domain\Products\Product\Attributes\Attribute\Terms\Term\Name(
								trim($name),
							);

							if (!$attribute->terms->has($termName)) {
								$term = new Domain\Products\Product\Attributes\Attribute\Terms\Term(
									$termName,
								);

								$attribute->terms->add($term);
							}
						}
					}
				}
			}
			*/

			if ($attributeSlug) {
				$attributeSlug = new Domain\Products\Product\AttributeSlugs\AttributeSlug(
					$attributeSlug,
				);

				if ($product->attributeSlugs->has($attributeSlug)) {
					$attributeSlug = $product->attributeSlugs->get($attributeSlug);
				} else {
					$product->attributeSlugs->add($attributeSlug);
				}

				if ($termSlug) {
					$termSlug = new Domain\Products\Product\AttributeSlugs\AttributeSlug\TermSlugs\TermSlug(
						$termSlug,
					);

					if (!$attributeSlug->termSlugs->has($termSlug)) {
						$attributeSlug->termSlugs->add($termSlug);
					}
				}
			}

			if ($brandId) {
				$brandId = new Domain\Products\Product\BrandIds\BrandId(
					$brandId,
				);

				if (!$product->brandIds->has($brandId)) {
					$product->brandIds->add($brandId);
				}
			}

			if ($categoryId) {
				$categoryId = new Domain\Products\Product\CategoryIds\CategoryId(
					$categoryId,
				);

				if (!$product->categoryIds->has($categoryId)) {
					$product->categoryIds->add($categoryId);
				}
			}

			if ($tagId) {
				$tagId = new Domain\Products\Product\TagIds\TagId(
					$tagId,
				);

				if (!$product->tagIds->has($tagId)) {
					$product->tagIds->add($tagId);
				}
			}
		}

		return $products;
	}
}