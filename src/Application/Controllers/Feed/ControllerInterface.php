<?php

namespace Hoo\ProductFeeds\Application\Controllers\Feed;

interface ControllerInterface
{
	public function __invoke(): string;

	public function path(): string;
}
