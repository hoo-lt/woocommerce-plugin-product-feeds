<?php

namespace Hoo\ProductFeeds\Application\Controllers\Term;

use Hoo\ProductFeeds\Application;
use Hoo\ProductFeeds\Domain;

class Controller implements ControllerInterface
{
	public function __construct(
		protected readonly Application\Mappers\TermMeta\MapperInterface $termMetaMapper,
		protected readonly Domain\Repositories\TermMeta\RepositoryInterface $termMetaRepository,
		protected readonly Application\Template\TemplateInterface $template,
	) {
	}

	public function template(int $id): string
	{
		return ($this->template)('/Term', [
			'icon' => $this->termMetaMapper->icon(
				$this->termMetaRepository->get($id)
			)
		]);
	}

	public function addTemplate(): string
	{
		return ($this->template)('/Term/Add', [
			'labels' => $this->termMetaMapper->labels(),
		]);
	}

	public function editTemplate(int $id): string
	{
		return ($this->template)('/Term/Edit', [
			'value' => $this->termMetaRepository->get($id)->value,
			'labels' => $this->termMetaMapper->labels(),
		]);
	}

	public function add(int $id, string $value): void
	{
		$this->termMetaRepository->set($id, Domain\TermMeta::from($value));
	}

	public function edit(int $id, string $value): void
	{
		$this->termMetaRepository->set($id, Domain\TermMeta::from($value));
	}
}
