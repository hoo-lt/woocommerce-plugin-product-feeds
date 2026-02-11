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
					id: (int) $row['id'],
					name: $row['name'],
					slug: $row['slug'],
					price: (float) $row['price'],
					stock: (int) $row['stock'],
					gtin: $row['gtin']
				);
				$products->add($product);
			}

			if (!$product->brands->has((int) $row['brand_id'])) {
				$product->brands->add(new Domain\Products\Product\Brands\Brand(
					(int) $row['brand_id'],
					$row['brand_name'],
					$row['brand_slug']
				));
			}

			if (!$product->categories->has((int) $row['category_id'])) {
				$product->categories->add(new Domain\Products\Product\Categories\Category(
					(int) $row['category_id'],
					$row['category_name'],
					$row['category_slug']
				));
			}
		}

		return $products;
	}
}