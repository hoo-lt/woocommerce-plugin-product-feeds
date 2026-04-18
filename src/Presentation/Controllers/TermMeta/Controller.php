<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Presentation\Controllers\TermMeta;

use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Presentation;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\View\ViewInterface;

readonly class Controller
{
	public function __construct(
		protected RequestInterface $request,
		protected ViewInterface $view,
		protected Presentation\Mapper\TermMeta\Mapper $termMetaMapper,
		protected Domain\Repository\TermMeta\RepositoryInterface $termMetaRepository,
	) {
	}

	public function index(int $id): string
	{
		return $this->view
			->withValue(
				'icon',
				$this->termMetaMapper->icon(
					$this->termMetaRepository->get($id)
				),
			)
		('term-meta.index');
	}

	public function add(): string
	{
		return $this->view
			->withValue(
				'options',
				$this->termMetaMapper->options(),
			)
		('term-meta.add');
	}

	public function edit(int $id): string
	{
		return $this->view
			->withValue(
				'selected',
				$this->termMetaMapper->option(
					$this->termMetaRepository->get($id)
				),
			)
			->withValue(
				'options',
				$this->termMetaMapper->options(),
			)
		('term-meta.edit');
	}

	public function post(int $id): void
	{
		$this->termMetaRepository->set(
			$id,
			Domain\TermMeta::from(
				$this->request->post(Domain\TermMeta::KEY)
			)
		);
	}
}
