<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products\Product;

enum StockStatus: string
{
	case InStock = 'instock';
	case OutOfStock = 'outofstock';
	case OnBackorder = 'onbackorder';
}