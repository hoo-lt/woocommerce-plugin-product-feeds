<?php

namespace Hoo\ProductFeeds\Infrastructure\Mappers\Term;

use Hoo\ProductFeeds\Application;
use Hoo\ProductFeeds\Domain;

class Mapper implements Application\Mappers\Term\MapperInterface
{
	public function label(Domain\Term $term): string
	{
		return match ($term) {
			Domain\Term::Include => __('Include', 'woocommerce-product-feeds'),
			Domain\Term::Exclude => __('Exclude', 'woocommerce-product-feeds'),
		};
	}

	public function labels(): array
	{
		foreach (Domain\Term::cases() as $term) {
			$labels[$term->value] = $this->label($term);
		}
		return $labels;
	}

	public function icon(Domain\Term $term): string
	{
		return match ($term) {
			Domain\Term::Include => 'status-completed',
			Domain\Term::Exclude => 'status-cancelled',
		};
	}

	public function icons(): array
	{
		foreach (Domain\Term::cases() as $term) {
			$labels[$term->value] = $this->icon($term);
		}
		return $labels;
	}
}