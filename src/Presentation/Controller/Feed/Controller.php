<?php

namespace Hoo\ProductFeeds\Presentation\Controller\Feed;

use Hoo\WordPressPluginFramework\Http;
use Hoo\ProductFeeds\Presentation;
use Hoo\ProductFeeds\Domain;

class Controller implements Presentation\Controller\Feed\ControllerInterface
{
	public function __construct(
		protected readonly Domain\Repository\Attribute\RepositoryInterface $attributeRepository,
		protected readonly Domain\Repository\Brand\RepositoryInterface $brandRepository,
		protected readonly Domain\Repository\Category\RepositoryInterface $categoryRepository,
		protected readonly Domain\Repository\Product\RepositoryInterface $productRepository,
		protected readonly Domain\Repository\Term\RepositoryInterface $termRepository,
		protected readonly Presentation\Mapper\Feed\MapperInterface $mapper,
		protected readonly string $path,
	) {
	}

	public function __invoke(): Http\ResponseInterface
	{
		return new Http\Response(
			[
				'Content-Type' => $this->mapper->contentType(),
			],
			$this->mapper->body(
				$this->attributeRepository->all(),
				$this->brandRepository->all(),
				$this->categoryRepository->all(),
				$this->productRepository->all(),
				$this->termRepository->all(),
			),
		);
	}

	public function path(): string
	{
		return $this->path;
	}
}
