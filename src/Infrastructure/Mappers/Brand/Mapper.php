<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Mappers\Brand;

use Hoo\WordPressPluginFramework\Http\Url\Url;
use Hoo\WordPressPluginFramework\Http\Url\UrlInterface;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;

class Mapper
{
	protected readonly Url $url;

	public function __construct(
		string $url,
		string $path,
	) {
		$this->url = Url::from($url)
			->withPath($path);
	}

	public function map(array $array): Domain\Brands
	{
		$brands = new Domain\Brands();

		foreach ($array as [
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