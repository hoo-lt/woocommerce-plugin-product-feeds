<?php

namespace Hoo\ProductFeeds\Presentation\Mappers\Feed\Kaina24Lt;

use Hoo\ProductFeeds\Domain;
use XMLWriter;

class Mapper
{
	public function all(Domain\Products $products): string
	{
		$xmlWriter = new XMLWriter;
		$xmlWriter->openMemory();

		$xmlWriter->startDocument('1.0', 'UTF-8');

		$this->products($products, $xmlWriter);

		$xmlWriter->endDocument();

		return $xmlWriter->outputMemory();
	}

	protected function products(Domain\Products $products, XMLWriter $xmlWriter): void
	{
		$xmlWriter->startElement('products');

		foreach ($products as $product) {
			$this->product($product, $xmlWriter);
		}

		$xmlWriter->endElement();
	}

	protected function product(Domain\Products\Product $product, XMLWriter $xmlWriter): void
	{
		$xmlWriter->startElement('product');
		$xmlWriter->writeAttribute('id', $product->id);

		$this->cdata('title', $product->name, $xmlWriter);
		$this->text('price', (string) $product->price, $xmlWriter); //temp typecasting
		$this->text('condition', 'new', $xmlWriter);
		$this->text('stock', (string) $product->stock, $xmlWriter); //temp typecasting
		$this->text('ean_code', $product->gtin, $xmlWriter);

		$this->manufacturer($product->brands, $xmlWriter);
		$this->category($product->categories, $xmlWriter);
		$this->specs($product->attributes, $xmlWriter);

		$xmlWriter->endElement();
	}

	protected function manufacturer(Domain\Products\Product\Brands $brands, XMLWriter $xmlWriter): void
	{
		$brand = $brands->first();
		if (!$brand) {
			return;
		}

		$this->cdata('manufacturer', $brand->name, $xmlWriter);
	}

	protected function specs(Domain\Products\Product\Attributes $attributes, XMLWriter $xmlWriter): void
	{
		$xmlWriter->startElement('specs');

		foreach ($attributes as $attribute) {
			$this->spec($attribute, $xmlWriter);
		}

		$xmlWriter->endElement();
	}

	protected function spec(Domain\Products\Product\Attributes\Attribute $attribute, XMLWriter $xmlWriter): void
	{
		$xmlWriter->startElement('spec');
		$xmlWriter->writeAttribute('name', $attribute->name);
		$xmlWriter->writeCData(implode(', ', $attribute->terms->all()));
		$xmlWriter->endElement();
	}

	protected function category(Domain\Products\Product\Categories $categories, XMLWriter $xmlWriter): void
	{
		$category = $categories->first();
		if (!$category) {
			return;
		}

		$this->text('category_id', $category->id, $xmlWriter);
		$this->cdata('category_name', $category->name, $xmlWriter);
		$this->cdata('category_link', $category->slug, $xmlWriter);
	}

	protected function cdata(string $name, string $content, XMLWriter $xmlWriter)
	{
		$xmlWriter->startElement($name);
		$xmlWriter->writeCdata($content);
		$xmlWriter->endElement();
	}

	protected function text(string $name, string $content, XMLWriter $xmlWriter)
	{
		$xmlWriter->startElement($name);
		$xmlWriter->text($content);
		$xmlWriter->endElement();
	}
}