<?php

namespace Hoo\ProductFeeds\Application\Repositories\Term;

use Hoo\ProductFeeds\Domain\Term;

interface RepositoryInterface
{
	public function get(int $id): Term;
	public function set(int $id, Term $term): void;
}