<?php

namespace Hoo\ProductFeeds\Infrastructure\Mappers\Brand;

use Hoo\ProductFeeds\Domain;

class Mapper
{
	public function all(array $table): Domain\Brands
	{
		$brands = new Domain\Brands();

		foreach ($table as [
			'id' => $id,
			'name' => $name,
			'url' => $url,
		]) {
			if (!$brands->has((int) $id)) {
				$brands->add(new Domain\Brands\Brand(
					(int) $id,
					$name,
					$url,
				));
			}
		}

		return $brands;
	}
}