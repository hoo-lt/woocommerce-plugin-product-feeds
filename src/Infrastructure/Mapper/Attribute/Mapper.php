<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Mapper\Attribute;

use Hoo\WooCommercePlugin\LtProductFeeds\Domain;

class Mapper
{
	public function all(array $json): Domain\Attributes
	{
		$attributes = new Domain\Attributes();

		foreach ($json as [
			'name' => $name,
			'slug' => $slug,
			'terms' => $terms,
		]) {
			$attribute = new Domain\Attributes\Attribute(
				new Domain\Attributes\Attribute\Slug(
					$slug,
				),
				$name,
			);

			$attributes->add($attribute);

			foreach ($terms as [
				'name' => $name,
				'slug' => $slug,
			]) {
				$term = new Domain\Attributes\Attribute\Terms\Term(
					new Domain\Attributes\Attribute\Terms\Term\Slug(
						$slug,
					),
					$name,
				);

				$attribute->terms->add($term);
			}
		}

		return $attributes;
	}
}