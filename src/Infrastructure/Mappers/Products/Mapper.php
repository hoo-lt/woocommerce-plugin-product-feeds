<?php

namespace Hoo\ProductFeeds\Infrastructure\Mappers\Products;

use Hoo\ProductFeeds\Domain;

class Mapper
{
	public function __invoke(array $rows): Domain\Products
	{
		$products = new Domain\Products();

		foreach ($rows as $row) {
			if ($products->has((int) $row['id'])) {
				$product = $products->get((int) $row['id']);
			} else {
				$product = new Domain\Products\Product(
					(int) $row['id'],
					$row['name'],
					$row['slug'],
					(float) $row['price'],
					(int) $row['stock'],
					$row['gtin']
				);
				$products->add($product);
			}

			if (isset($row['attribute_id'])) {
				if ($product->attributes->has((int) $row['attribute_id'])) {
					$attribute = $product->attributes->get((int) $row['attribute_id']);
				} else {
					$attribute = new Domain\Products\Product\Attributes\Attribute(
						(int) $row['attribute_id'],
						$row['attribute_name'],
						$row['attribute_slug']
					);
					$product->attributes->add($attribute);
				}
			}

			if (isset($row['brand_id'])) {
				if (!$product->brands->has((int) $row['brand_id'])) {
					$product->brands->add(new Domain\Products\Product\Brands\Brand(
						(int) $row['brand_id'],
						$row['brand_name'],
						$row['brand_slug']
					));
				}
			}

			if (isset($row['category_id'])) {
				if (!$product->categories->has((int) $row['category_id'])) {
					$product->categories->add(new Domain\Products\Product\Categories\Category(
						(int) $row['category_id'],
						$row['category_name'],
						$row['category_slug']
					));
				}
			}
		}

		return $products;
	}
}