<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products\Product\TaxonomyAttributes\TaxonomyAttribute\Terms\Term;

use Hoo\WordPressPluginFramework\Collection;

readonly class Slug implements Collection\Item\Key\KeyInterface
{
	public function __construct(
		protected string $slug,
	) {
	}

	public function __invoke(): string
	{
		return $this->slug;
	}
}