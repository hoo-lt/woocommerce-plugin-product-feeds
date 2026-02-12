<?php

namespace Hoo\ProductFeeds\Domain\Repositories\TermMeta;

use Hoo\ProductFeeds\Domain;

interface RepositoryInterface
{
	public function get(int $id): Domain\TermMeta;
	public function set(int $id, Domain\TermMeta $termMeta): void;
}