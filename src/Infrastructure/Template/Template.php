<?php

namespace Hoo\ProductFeeds\Infrastructure\Template;

use Hoo\ProductFeeds\Application;

class Template implements Application\Template\TemplateInterface
{
	protected string $path;

	public function __construct()
	{
		$this->path = __DIR__ . '/Templates';
	}

	public function __invoke(string $template, array $array): string
	{
		$path = "{$this->path}/{$template}/Template.php";
		if (!file_exists($path)) {
			trigger_error("Template not found: $path", E_USER_WARNING);
		}

		ob_start();

		(static function ($path, $array) {
			extract($array);

			require($path);
		})($path, $array);

		return ob_get_clean();
	}
}