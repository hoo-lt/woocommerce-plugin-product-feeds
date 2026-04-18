<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Presentation\Mapper\Feed\Kaina24Lt;

use Hoo\WordPressPluginFramework\Http\Url\UrlInterface;
use Hoo\WooCommercePlugin\LtProductFeeds\Domain;
use Hoo\WooCommercePlugin\LtProductFeeds\Presentation;
use XMLWriter;

class Mapper implements Presentation\Mapper\Feed\MapperInterface
{
	public function __construct(
		protected readonly XMLWriter $xmlWriter,
		protected readonly string $utmSource,
		protected readonly string $utmMedium,
	) {
	}

	public function contentType(): string
	{
		return 'application/xml; charset=utf-8';
		return [
			'Content-Type: application/xml; charset=utf-8'
		];
	}

	public function body(
		Domain\Attributes $attributes,
		Domain\Brands $brands,
		Domain\Categories $categories,
		Domain\Products $products,
		Domain\Terms $terms,
	): string {
		$this->xmlWriter->openMemory();

		$this->xmlWriter->startDocument('1.0', 'UTF-8');

		$this->products(
			$attributes,
			$brands,
			$categories,
			$products,
			$terms,
		);

		$this->xmlWriter->endDocument();

		return $this->xmlWriter->outputMemory();
	}

	protected function products(
		Domain\Attributes $attributes,
		Domain\Brands $brands,
		Domain\Categories $categories,
		Domain\Products $products,
		Domain\Terms $terms,
	): void {
		$this->xmlWriter->startElement('products');

		foreach ($products as $product) {
			$this->product(
				$attributes,
				$brands,
				$categories,
				$product,
				$terms,
			);
		}

		$this->xmlWriter->endElement();
	}

	protected function product(
		Domain\Attributes $attributes,
		Domain\Brands $brands,
		Domain\Categories $categories,
		Domain\Products\Product $product,
		Domain\Terms $terms,
	): void {
		$this->xmlWriter->startElement('product');
		$this->xmlWriter->writeAttribute('id', $product->id());

		$this->cdata('title', $product->name);
		$this->text('price', (string) $product->price); //temp typecasting
		$this->text('condition', 'new');
		if ($product->stock) {
			$this->text('stock', (string) $product->stock); //temp typecasting
		}
		if ($product->gtin) {
			$this->text('ean_code', $product->gtin);
		}

		$this->manufacturer($brands, $product);

		$this->cdata('product_url', $this->utmUrl($product->url));

		$this->category($categories, $product);
		$this->specs($attributes, $product, $terms);

		$this->xmlWriter->endElement();
	}

	protected function manufacturer(
		Domain\Brands $brands,
		Domain\Products\Product $product,
	): void {
		$brand = $product->brands->first();
		if (!$brand) {
			return;
		}

		if (!$brands->has($brand->key())) {
			return;
		}

		$brand = $brands->get($brand->key());

		$this->cdata('manufacturer', $brand->name);
	}

	protected function category(
		Domain\Categories $categories,
		Domain\Products\Product $product,
	): void {
		$category = $product->categories->first();
		if (!$category) {
			return;
		}

		if (!$categories->has($category->key())) {
			return;
		}

		$category = $categories->get($category->key());

		$this->text('category_id', $category->id());
		$this->cdata('category_name', $category->name);
		$this->cdata('category_link', $this->utmUrl($category->url));
	}

	protected function specs(
		Domain\Attributes $attributes,
		Domain\Products\Product $product,
		Domain\Terms $terms,
	): void {
		$terms = clone $terms; //logical error - collection contains all terms and cannot be cleaned by iterating throght product attribute terms

		$this->xmlWriter->startElement('specs');

		foreach ($product->attributes as $attribute) {
			foreach ($attribute->terms as $term) {
				if ($terms->has($term->key())) {
					continue;
				}

				$terms->remove($term->key());
			}

			if (!$attributes->has($attribute->key())) {
				continue;
			}

			$attribute = $attributes->get($attribute->key());

			$this->spec($attribute, $terms);
		}

		$this->xmlWriter->endElement();
	}

	protected function spec(
		Domain\Attributes\Attribute $attribute,
		Domain\Terms $terms,
	): void {
		$this->xmlWriter->startElement('spec');
		$this->xmlWriter->writeAttribute('name', $attribute->name);
		//$this->xmlWriter->writeCData(implode(', ', array_map(fn($term) => $term->name, $terms->all())));
		$this->xmlWriter->endElement();
	}

	protected function cdata(string $name, string $content)
	{
		$this->xmlWriter->startElement($name);
		$this->xmlWriter->writeCdata($content);
		$this->xmlWriter->endElement();
	}

	protected function text(string $name, string $content)
	{
		$this->xmlWriter->startElement($name);
		$this->xmlWriter->text($content);
		$this->xmlWriter->endElement();
	}

	protected function utmUrl(UrlInterface $url): UrlInterface
	{
		return $url
			->withQueryValue('utm_source', $this->utmSource)
			->withQueryValue('utm_medium', $this->utmMedium);
	}
}