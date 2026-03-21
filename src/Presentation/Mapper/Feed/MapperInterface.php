<?php

namespace Hoo\ProductFeeds\Presentation\Mapper\Feed;

use Hoo\ProductFeeds\Domain;

interface MapperInterface
{
	public function contentType(): string;
	public function body(
		Domain\Attributes $attributes,
		Domain\Brands $brands,
		Domain\Categories $categories,
		Domain\Products $products,
		Domain\Terms $terms,
	): string;
}