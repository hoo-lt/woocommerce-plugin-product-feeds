<?php

namespace Hoo\ProductFeeds\Domain;

enum Taxonomy: string
{
	case Brand = 'product_brand';
	case Category = 'product_cat';
	case Tag = 'product_tag';
}