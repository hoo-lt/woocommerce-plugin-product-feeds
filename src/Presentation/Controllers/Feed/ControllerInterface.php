<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Presentation\Controller\Feed;

use Hoo\WordPressPluginFramework\Http\Response\ResponseInterface;

interface ControllerInterface
{
	public function __invoke(): ResponseInterface;

	public function path(): string;
}
