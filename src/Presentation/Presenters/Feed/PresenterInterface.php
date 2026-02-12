<?php

namespace Hoo\ProductFeeds\Presentation\Presenters\Feed;

interface PresenterInterface
{
	public function present(): string;

	public function path(): string;
}
