<?php

namespace Hoo\ProductFeeds\Infrastructure\Mappers\TermTaxonomy;

class Mapper
{
	public function __invoke(array $table): array
	{
		return array_map(fn($row) => (int) $row['term_taxonomy_id'], $table);
	}
}