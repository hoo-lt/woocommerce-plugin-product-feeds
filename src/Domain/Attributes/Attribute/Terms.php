<?php

namespace Hoo\ProductFeeds\Domain;

use Hoo\WordPressPluginFramework\Collection;

class Terms extends Collection\AbstractCollection
{
	public function __construct(
		Terms\Term ...$terms,
	) {
		$this->items = $terms;
	}

	public function get(Collection\Item\Key\KeyInterface $key): Terms\Term
	{
		return parent::get($key);
	}

	public function first(): ?Terms\Term
	{
		return parent::first();
	}

	public function last(): ?Terms\Term
	{
		return parent::last();
	}

	public function add(Terms\Term $term): void
	{
		$key = $term->key();
		if ($this->has($key)) {
			return;
		}

		$this->items[$key()] = $term;
	}
}