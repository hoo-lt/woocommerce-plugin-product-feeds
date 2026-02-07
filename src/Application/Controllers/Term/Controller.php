<?php

namespace Hoo\ProductFeeds\Application\Controllers\Term;

use Hoo\ProductFeeds\Application;
use Hoo\ProductFeeds\Domain;

class Controller implements ControllerInterface
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
			'icon' => $this->icon($id),
		]);
	}

	public function addTemplate(): string
	{
		return ($this->template)('/Term/Add', [
			'labels' => $this->labels(),
		]);
	}

	public function editTemplate(int $id): string
	{
		return ($this->template)('/Term/Edit', [
			'value' => $this->value($id),
			'labels' => $this->labels(),
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

	protected function value(int $id): string
	{
		return $this->repository->get($id)->value;
	}

	protected function labels(): array
	{
		$labels = $this->mapper->labels();

		return array_map(fn($value, $label) => [
			'value' => $value,
			'label' => $label,
		], array_keys($labels), array_values($labels));
	}

	protected function icon(int $id): string
	{
		return $this->mapper->icon(
			$this->repository->get($id)
		);
	}
}
