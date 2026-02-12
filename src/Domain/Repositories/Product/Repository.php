<?php

namespace Hoo\ProductFeeds\Domain\Repositories\Product;

use Hoo\ProductFeeds\Domain;

interface RepositoryInterface
{
	public function __invoke(): Domain\Products;
}