<?php

namespace Hoo\ProductFeeds\Infrastructure\Mappers\Category;

use Hoo\ProductFeeds\Domain;

class Mapper
{
	public function all(array $table): Domain\Categories
	{
		$categories = new Domain\Categories();

		foreach ($table as [
			'id' => $id,
			'name' => $name,
			'url' => $url,
		]) {
			if (!$categories->has((int) $id)) {
				$categories->add(new Domain\Categories\Category(
					(int) $id,
					$name,
					$url,
				));
			}
		}

		return $categories;
	}
}