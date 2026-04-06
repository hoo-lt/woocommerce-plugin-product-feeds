<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products\Product;

readonly class Price
{
	protected int $time;

	public function __construct(
		protected ?float $regular,
		protected ?float $sale,
		protected ?int $saleDatesFrom,
		protected ?int $saleDatesTo,
	) {
		$this->time = time();
	}

	public function __invoke(): ?float
	{
		if (
			$this->sale !== null &&
			(
				$this->saleDatesFrom === null ||
				$this->time >= $this->saleDatesFrom
			) &&
			(
				$this->saleDatesTo === null ||
				$this->time <= $this->saleDatesTo
			)
		) {
			return $this->sale;
		}

		return $this->regular;
	}
}
