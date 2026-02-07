<?php

namespace Hoo\ProductFeeds\Application;

interface TemplateInterface
{
	public function __invoke(string $template, array $array): string;
}