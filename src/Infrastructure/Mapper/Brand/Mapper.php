<?php

namespace Hoo\ProductFeeds\Infrastructure\Mapper\Brand;

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

	public function all(array $table): Domain\Brands
	{
		$brands = new Domain\Brands();

		foreach ($table as [
			'id' => $id,
			'parent_id' => $parentId,
			'name' => $name,
			'path' => $path,
		]) {
			$id = new Domain\Brands\Brand\Id(
				$id
			);

			if ($brands->has($id)) {
				continue;
			}

			$parentId = $parentId ? new Domain\Brands\Brand\Id(
				$parentId
			) : null;

			$brands->add(new Domain\Brands\Brand(
				$id,
				$parentId,
				$name,
				$this->url->withPath("{$this->url->path()}/{$path}"),
			));
		}

		return $brands;
	}
}