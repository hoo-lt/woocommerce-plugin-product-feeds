<?php

namespace Hoo\ProductFeeds\Presentation\Controller\Feed;

use Hoo\WordPressPluginFramework\Http;

interface ControllerInterface
{
	public function __invoke(): Http\ResponseInterface;

	public function path(): string;
}
