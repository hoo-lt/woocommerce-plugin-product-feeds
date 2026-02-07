<?php

namespace Hoo\ProductFeeds\Application\Controllers\Term;

interface ControllerInterface
{
	public function template(int $id): string;
	public function addTemplate(): string;
	public function editTemplate(int $id): string;
	public function add(int $id, string $value): void;
	public function edit(int $id, string $value): void;
}
