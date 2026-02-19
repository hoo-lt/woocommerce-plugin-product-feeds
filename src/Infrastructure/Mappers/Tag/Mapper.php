<?php

namespace Hoo\ProductFeeds\Infrastructure\Mappers\Tag;

use Hoo\ProductFeeds\Domain;

class Mapper
{
	public function all(array $table): Domain\Tags
	{
		$tags = new Domain\Tags();

		foreach ($table as [
			'id' => $id,
			'name' => $name,
			'url' => $url,
		]) {
			if (!$tags->has((int) $id)) {
				$tags->add(new Domain\Tags\Tag(
					(int) $id,
					$name,
					$url,
				));
			}
		}

		return $tags;
	}
}