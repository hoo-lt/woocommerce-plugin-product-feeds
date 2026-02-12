<?php

namespace Hoo\ProductFeeds\Infrastructure\Mappers\Product;

use Hoo\ProductFeeds\Domain;

class Mapper
{
	public function __invoke(array $table): Domain\Products
	{
		$products = new Domain\Products();

		foreach ($table as $row) {
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

				if (isset($row['term_id'])) {
					if (!$attribute->terms->has($row['term_id'])) {
						$attribute->terms->add(new Domain\Products\Product\Attributes\Attribute\Terms\Term(
							(int) $row['term_id'],
							$row['term_name'],
							$row['term_slug']
						));
					}
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