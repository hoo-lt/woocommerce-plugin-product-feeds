<?php

namespace Hoo\ProductFeeds\Application\Mappers\Term;

use Hoo\ProductFeeds\Domain;

interface MapperInterface
{
	public function label(Domain\Term $term): string;
	public function labels(): array;

	public function icon(Domain\Term $term): string;
	public function icons(): array;
}