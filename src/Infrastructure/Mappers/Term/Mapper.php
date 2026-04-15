<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Mappers\Term;

use Hoo\WooCommercePlugin\LtProductFeeds\Domain;

class Mapper
{
	public function map(array $array): Domain\Terms
	{
		$terms = new Domain\Terms();

		foreach ($array as [
			'id' => $id,
			'name' => $name,
		]) {
			$id = new Domain\Terms\Term\Id(
				$id
			);

			if ($terms->has($id)) {
				continue;
			}

			$terms->add(new Domain\Terms\Term(
				$id,
				$name,
			));
		}

		return $terms;
	}
}