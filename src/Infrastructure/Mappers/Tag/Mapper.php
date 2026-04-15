<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Mappers\Tag;

use Hoo\WordPressPluginFramework\Http;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;

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

	public function map(array $array): Domain\Tags
	{
		$tags = new Domain\Tags();

		foreach ($array as [
			'id' => $id,
			'parent_id' => $parentId,
			'name' => $name,
			'path' => $path,
		]) {
			$id = new Domain\Tags\Tag\Id(
				$id
			);

			if ($tags->has($id)) {
				continue;
			}

			$parentId = $parentId ? new Domain\Tags\Tag\Id(
				$parentId
			) : null;

			$tags->add(new Domain\Tags\Tag(
				$id,
				$parentId,
				$name,
				$this->url->withPath("{$this->url->path()}/{$path}"),
			));
		}

		return $tags;
	}
}