<?php

namespace Hoo\ProductFeeds\Presentation\Presenters\Feed\Kaina24Lt;

use Hoo\ProductFeeds\Presentation;
use Hoo\ProductFeeds\Domain;

class Presenter implements Presentation\Presenters\Feed\PresenterInterface
{
	public function __construct(
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
		return $this->kaina24LtMappers->all(
			$this->productRepository->all(),
		);
	}
}
