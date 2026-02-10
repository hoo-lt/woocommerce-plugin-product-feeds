<?php

namespace Hoo\ProductFeeds\Infrastructure\Database;

use Hoo\ProductFeeds\Infrastructure;

interface DatabaseInterface
{
	public function select(Infrastructure\Database\Queries\QueryInterface $query): array;
}