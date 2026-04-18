<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Presentation\Actions\TermMeta\Post;

use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;

readonly class Action
{
	public function __construct(
		protected RequestInterface $request,
		protected Domain\Repository\TermMeta\RepositoryInterface $termMetaRepository,
	) {
	}

	public function __invoke(int $id): void
	{
		$this->termMetaRepository->set(
			$id,
			Domain\TermMeta::from(
				$this->request->post(Domain\TermMeta::KEY)
			)
		);
	}
}
