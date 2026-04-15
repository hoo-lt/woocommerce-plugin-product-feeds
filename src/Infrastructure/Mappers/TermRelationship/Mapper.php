<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Mappers\TermRelationship;

class Mapper
{
	public function map(array $array): array
	{
		return array_map(fn($row) => (int) $row['object_id'], $array);
	}
}