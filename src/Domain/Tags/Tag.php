<?php

namespace Hoo\ProductFeeds\Domain\Tags;

class Tag
{
	public function __construct(
		public int $id,
		public string $name,
		public string $url,
	) {
	}
}