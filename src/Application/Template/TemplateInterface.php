<?php

namespace Hoo\ProductFeeds\Application\Template;

interface TemplateInterface
{
	public function __invoke(string $template, array $array): string;
}