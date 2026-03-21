<?php

namespace Hoo\ProductFeeds\Infrastructure\Mapper\Category;

use Hoo\WordPressPluginFramework\Http;
use Hoo\ProductFeeds\Domain;

class Mapper
{
	protected readonly Http\Url $url;

	public function __construct(
		string $url,
		string $path,
	) {
		$this->url = Http\Url::from($url)
			->withPath($path);
	}

	public function all(array $table): Domain\Categories
	{
		$categories = new Domain\Categories();

		foreach ($table as [
			'id' => $id,
			'parent_id' => $parentId,
			'name' => $name,
			'path' => $path,
		]) {
			$id = new Domain\Categories\Category\Id(
				$id
			);

			if ($categories->has($id)) {
				continue;
			}

			$parentId = $parentId ? new Domain\Categories\Category\Id(
				$parentId
			) : null;

			$categories->add(new Domain\Categories\Category(
				$id,
				$parentId,
				$name,
				$this->url->withPath("{$this->url->path()}/{$path}"),
			));
		}

		return $categories;
	}
}