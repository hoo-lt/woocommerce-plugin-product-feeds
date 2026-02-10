<?php

namespace Hoo\ProductFeeds\Infrastructure\Database\Queries;

interface QueryInterface
{
	public function __invoke(): string;
}