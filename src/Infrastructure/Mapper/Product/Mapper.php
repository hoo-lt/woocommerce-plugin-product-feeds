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
			'name' => $name,
			'path' => $path,
			'price' => $price,
			'stock' => $stock,
			'gtin' => $gtin,
			'attribute_taxonomy' => $attributeTaxonomy,
			'term_id' => $termId,
			'brand_id' => $brandId,
			'category_id' => $categoryId,
			//'tag_id' => $tagId,
		]) {
			$id = new Domain\Products\Product\Id(
				$id,
			);

			if ($products->has($id)) {
				$product = $products->get($id);
			} else {
				$product = new Domain\Products\Product(
					$id,
					$name,
					$this->url->withPath("{$this->url->path()}/{$path}"),
					$price,
					$stock,
					$gtin,
				);
				$products->add($product);
			}

			if ($attributeTaxonomy) {
				$attributeSlug = strtr($attributeTaxonomy, [
					'pa_' => '',
				]);

				$attributeSlug = new Domain\Products\Product\AttributeSlugs\AttributeSlug(
					$attributeSlug,
				);

				if ($product->attributeSlugs->has($attributeSlug)) {
					$attributeSlug = $product->attributeSlugs->get($attributeSlug);
				} else {
					$product->attributeSlugs->add($attributeSlug);
				}

				if ($termId) {
					$termId = new Domain\Products\Product\AttributeSlugs\AttributeSlug\TermIds\TermId(
						$termId,
					);

					if (!$attributeSlug->termIds->has($termId)) {
						$attributeSlug->termIds->add($termId);
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

			/*
			if ($tagId) {
				$tagId = new Domain\Products\Product\Tags\Tag\Id(
					$tagId,
				);

				if (!$product->tags->has($tagId)) {
					$product->tags->add(new Domain\Products\Product\Tags\Tag(
						$tagId,
					));
				}
			}
			*/
		}

		return $products;
	}
}