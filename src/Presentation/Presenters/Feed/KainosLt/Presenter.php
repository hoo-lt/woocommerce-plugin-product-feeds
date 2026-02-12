<?php

namespace Hoo\ProductFeeds\Presentation\Presenters\Product\KainosLt;

use Hoo\ProductFeeds\Presentation;
use Hoo\ProductFeeds\Domain;

class Presenter implements Presentation\Presenters\Product\PresenterInterface
{
	public function __construct(
		protected readonly Domain\Repositories\Product\RepositoryInterface $productRepository,
	) {
	}

	public function path(): string
	{
		return 'kaina24-lt.xml';
	}

	public function present(): string
	{
		$products = $this->productRepository->all();

		return '';
	}
}
