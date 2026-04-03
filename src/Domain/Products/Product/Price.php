<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Domain\Products\Product;

readonly class Price
{
	protected int $time;

	public function __construct(
		protected ?float $regularPrice,
		protected ?float $salePrice,
		protected ?int $salePriceDatesFrom,
		protected ?int $salePriceDatesTo,
	) {
		$this->time = time();
	}

	public function __invoke(): ?float
	{
		if (
			$this->salePrice !== null &&
			(
				$this->salePriceDatesFrom === null ||
				$this->time >= $this->salePriceDatesFrom
			) &&
			(
				$this->salePriceDatesTo === null ||
				$this->time <= $this->salePriceDatesTo
			)
		) {
			return $this->salePrice;
		}

		return $this->regularPrice;
	}

	public function discount(): float
	{
		return $this->regularPrice !== null ? round($this->regularPrice - ($this)(), 2) : 0.0;
	}

	public function discountPercent(): float
	{
		if (
			$this->regularPrice === null ||
			$this->regularPrice === 0.0
		) {
			return 0.0;
		}

		return round($this->discount() / $this->regularPrice * 100, 2);
	}
}
