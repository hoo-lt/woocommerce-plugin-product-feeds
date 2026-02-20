<?php

namespace Hoo\ProductFeeds\Presentation\Presenters\Feed\Kaina24Lt;

use Hoo\ProductFeeds\Presentation;
use Hoo\ProductFeeds\Domain;

class Presenter implements Presentation\Presenters\Feed\PresenterInterface
{
	public function __construct(
		protected readonly Domain\Repositories\Brand\RepositoryInterface $brandRepository,
		protected readonly Domain\Repositories\Category\RepositoryInterface $categoryRepository,
		protected readonly Domain\Repositories\Product\RepositoryInterface $productRepository,
		protected readonly Presentation\Mappers\Feed\Kaina24Lt\Mapper $kaina24LtMappers,
	) {
	}

	public function path(): string
	{
		return 'kaina24-lt.xml';
	}

	public function present(): string
	{
		header('Content-Type: application/xml; charset=utf-8');

		return $this->kaina24LtMappers->all(
			$this->brandRepository->all(),
			$this->categoryRepository->all(),
			$this->productRepository->all(),
		);
	}
}
