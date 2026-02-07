<?php

namespace Hoo\ProductFeeds\Application\Controllers\Term;

use Hoo\ProductFeeds\Domain;
use Hoo\ProductFeeds\Application;

class Controller
{
	public function __construct(
		protected readonly Application\Mappers\Term\MapperInterface $mapper,
		protected readonly Application\Repositories\Term\RepositoryInterface $repository,
		protected readonly Application\TemplateInterface $template,
	) {
	}

	public function template(int $id): string
	{
		return ($this->template)('/Term', [
			'icon' => $this->mapper->icon(
				$this->repository->get($id)
			),
		]);
	}

	public function addTemplate(): string
	{
		$labels = $this->mapper->labels();

		return ($this->template)('/Term/Add', [
			'value' => null,
			'options' => array_map(fn($value, $label) => [
				'value' => $value,
				'label' => $label,
			], array_keys($labels), array_values($labels)),
		]);
	}

	public function editTemplate(int $id): string
	{
		$labels = $this->mapper->labels();

		return ($this->template)('/Term/Edit', [
			'value' => $this->repository->get($id)->value,
			'options' => array_map(fn($value, $label) => [
				'value' => $value,
				'label' => $label,
			], array_keys($labels), array_values($labels)),
		]);
	}

	public function add(int $id, string $value): void
	{
		$this->repository->set($id, Domain\Term::from($value));
	}

	public function edit(int $id, string $value): void
	{
		$this->repository->set($id, Domain\Term::from($value));
	}
}
