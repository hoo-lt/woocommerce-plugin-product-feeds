<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products\Product;

enum Status: string
{
	case Published = 'publish';
	case Future = 'future';
	case Draft = 'draft';
	case Pending = 'pending';
	case Private = 'private';
	case Trash = 'trash';
	case AutoDraft = 'auto-draft';
	case Inherit = 'inherit';
}