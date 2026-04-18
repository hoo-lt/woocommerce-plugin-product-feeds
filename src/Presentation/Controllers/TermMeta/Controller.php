<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Presentation\Controllers\TermMeta;

use Hoo\WordPressPluginFramework\Http\RequestInterface;
use Hoo\WordPressPluginFramework\View\ViewInterface;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Presentation;

class Controller
{
	public function __construct(
		protected readonly RequestInterface $request,
		protected readonly ViewInterface $view,
		protected readonly Presentation\Mapper\TermMeta\Mapper $termMetaMapper,
		protected readonly Domain\Repository\TermMeta\RepositoryInterface $termMetaRepository,
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

	public function set(int $id): void
	{
		$value = $this->request->post(Domain\TermMeta::KEY);
		if ($value) {
			$this->termMetaRepository->set($id, Domain\TermMeta::from($value));
		}
	}
}
