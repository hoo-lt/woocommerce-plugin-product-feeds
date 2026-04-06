<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products\Product;

readonly class Stock
{
	public function __construct(
		public Stock\Manage $manage,
		public Stock\Status $status,
		public ?int $quantity,
	) {
	}
}
