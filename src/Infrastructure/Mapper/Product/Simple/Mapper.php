<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Mapper\Product\Simple;

use Hoo\WordPressPluginFramework\Http;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;

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

	public function all(array $rows): Domain\Products
	{
		$products = new Domain\Products();

		foreach ($rows as $row) {
			$product = new Domain\Products\Product(
				new Domain\Products\Product\Id($row['id']),
				null,
				$row['name'],
				$row['description'],
				$this->url->withPath("{$this->url->path()}/{$row['slug']}"),
				Domain\Products\Product\Status::from($row['status']),
				new Domain\Products\Product\Price(
					$row['price']['regular'],
					$row['price']['sale'],
					$row['price']['sale_dates_from'],
					$row['price']['sale_dates_to'],
				),
				$row['sku'],
				$row['gtin'],
				new Domain\Products\Product\Stock(
					Domain\Products\Product\Stock\Manage::from($row['stock']['manage']),
					Domain\Products\Product\Stock\Status::from($row['stock']['status']),
					$row['stock']['quantity'],
				),
			);

			if ($row['image_ids']['image_id']) {
				$product->imageIds->add(
					new Domain\Products\Product\ImageIds\ImageId($row['image_ids']['image_id']),
				);
			}

			foreach (array_filter(array_map(fn($imageId) => (int) $imageId, explode(',', $row['image_ids']['image_ids'] ?? ''))) as $imageId) {
				$product->imageIds->add(
					new Domain\Products\Product\ImageIds\ImageId($imageId),
				);
			}

			foreach ($row['brand_ids'] as $brandId) {
				$product->brandIds->add(
					new Domain\Products\Product\BrandIds\BrandId($brandId),
				);
			}

			foreach ($row['category_ids'] as $categoryId) {
				$product->categoryIds->add(
					new Domain\Products\Product\CategoryIds\CategoryId($categoryId),
				);
			}

			foreach ($row['tag_ids'] as $tagId) {
				$product->tagIds->add(
					new Domain\Products\Product\TagIds\TagId($tagId),
				);
			}

			$productAttributes = unserialize($row['product_attributes']) ?: [];

			foreach (array_filter($productAttributes, fn($productAttribute) => !$productAttribute['is_taxonomy']) as $productAttribute) {
				$attribute = new Domain\Products\Product\Attributes\Attribute(
					new Domain\Products\Product\Attributes\Attribute\Name($productAttribute['name']),
					$productAttribute['is_visible'] ?? false,
				);

				foreach (array_filter(array_map(fn($value) => trim($value), explode('|', $productAttribute['value']))) as $value) {
					$attribute->terms->add(
						new Domain\Products\Product\Attributes\Attribute\Terms\Term(
							new Domain\Products\Product\Attributes\Attribute\Terms\Term\Name($value),
						),
					);
				}

				$product->attributes->add($attribute);
			}

			foreach ($row['attributes'] as $attribute) {
				$productAttribute = $productAttributes["pa_{$attribute['slug']}"];

				$taxonomyAttribute = new Domain\Products\Product\TaxonomyAttributes\TaxonomyAttribute(
					new Domain\Products\Product\TaxonomyAttributes\TaxonomyAttribute\Slug($attribute['slug']),
					$productAttribute['is_visible'] ?? false,
				);

				foreach ($attribute['terms'] as $term) {
					$taxonomyAttribute->terms->add(
						new Domain\Products\Product\TaxonomyAttributes\TaxonomyAttribute\Terms\Term(
							new Domain\Products\Product\TaxonomyAttributes\TaxonomyAttribute\Terms\Term\Slug($term['slug']),
						),
					);
				}

				$product->taxonomyAttributes->add($taxonomyAttribute);
			}

			$products->add($product);
		}

		return $products;
	}
}