<?php

add_action('init', function () {
	add_feed('kaina24', function () {
		if (!class_exists('WooCommerce')) {
			return;
		}

		header('Content-Type: application/xml; charset=utf-8');

		$xmlWriter = new XMLWriter;
		$xmlWriter->openMemory();
		$xmlWriter->setIndent(true);

		$xmlWriter->startDocument('1.0', 'UTF-8');
		$xmlWriter->startElement('products');

		$products = wc_get_products([
			'status' => 'publish',
			'limit' => -1,
			'type' => [
				'simple',
				'variation',
			],
		]);

		foreach ($products as $product) {
			$product_id = $product->get_id();

			$is_variation = $product->is_type('variation');
			if ($is_variation) {
				$parent_product_id = $product->get_parent_id();
				$parent_product = wc_get_product($parent_product_id);

				if (!$parent_product) {
					continue;
				}

				if (!$parent_product || $parent_product->get_status() !== 'publish') {
					continue;
				}
			}

			$xmlWriter->startElement('product');
			$xmlWriter->writeAttribute('id', $product_id);

			title($xmlWriter, $product);
			price($xmlWriter, $product);
			condition($xmlWriter);
			stock($xmlWriter, $product);
			ean_code($xmlWriter, $product);
			manufacturer($xmlWriter, $is_variation, $parent_product_id, $product_id);
			image_url($xmlWriter, $is_variation, $parent_product, $product);
			product_url($xmlWriter, $product);
			category($xmlWriter, $is_variation, $parent_product_id, $product_id);
			specs($xmlWriter, $is_variation, $parent_product, $product);
			delivery($xmlWriter);

			$xmlWriter->endElement(); // product
		}

		$xmlWriter->endElement();
		$xmlWriter->endDocument();

		echo $xmlWriter->outputMemory();
	});
});

function title($xmlWriter, $product)
{
	$xmlWriter->startElement('title');
	$xmlWriter->writeCData($product->get_name());
	$xmlWriter->endElement();
}

function price($xmlWriter, $product)
{
	$xmlWriter->writeElement('price', $product->get_price());
}

function condition($xmlWriter)
{
	$xmlWriter->writeElement('condition', 'new');
}

function stock($xmlWriter, $product)
{
	$stock = $product->get_stock_quantity();
	if (!$stock) {
		return;
	}

	$xmlWriter->writeElement('stock', $stock);
}

function ean_code($xmlWriter, $product)
{
	$ean_code = $product->get_meta('_global_unique_id');
	if (!$ean_code) {
		return;
	}

	$xmlWriter->writeElement('ean_code', $ean_code);
}

function manufacturer($xmlWriter, $is_variation, $parent_product_id, $product_id)
{
	$brands = wc_get_product_terms($is_variation ? $parent_product_id : $product_id, 'product_brand');
	if (!$brands) {
		return;
	}

	$brand = $brands[0];

	$xmlWriter->startElement('manufacturer');
	$xmlWriter->writeCData($brand->name);
	$xmlWriter->endElement();
}

function image_url($xmlWriter, $is_variation, $parent_product, $product)
{
	$xmlWriter->startElement('image_url');
	$xmlWriter->writeCData(wp_get_attachment_url($is_variation ? $parent_product->get_image_id() : $product->get_image_id()));
	$xmlWriter->endElement();
}

function product_url($xmlWriter, $product)
{
	$xmlWriter->startElement('product_url');
	$xmlWriter->writeCData($product->get_permalink());
	$xmlWriter->endElement();
}

function category($xmlWriter, $is_variation, $parent_product_id, $product_id)
{
	$categories = wc_get_product_terms($is_variation ? $parent_product_id : $product_id, 'product_cat');
	if (!$categories) {
		return;
	}

	$category = $categories[0];

	$xmlWriter->writeElement('category_id', $category->term_id);

	$xmlWriter->startElement('category_name');
	$xmlWriter->writeCData($category->name);
	$xmlWriter->endElement();

	$xmlWriter->startElement('category_link');
	$xmlWriter->writeCData(get_term_link($category));
	$xmlWriter->endElement();
}

function specs($xmlWriter, $is_variation, $parent_product, $product)
{
	$xmlWriter->startElement('specs');

	$attributes = $is_variation ? $parent_product->get_attributes() : $product->get_attributes();

	foreach ($attributes as $attribute) {
		$attribute_name = $attribute->get_name();
		$attribute_options = $attribute->get_options();

		$attribute_label = wc_attribute_label($attribute_name);

		$term = $attribute->get_variation() ? $product->get_attribute($attribute_name) : implode(', ', $attribute->is_taxonomy() ? array_map(function ($attribute_option) use ($attribute_name) {
			$term = get_term($attribute_option, $attribute_name);

			return $term instanceof WP_Term ? $term->name : $attribute_name;
		}, $attribute_options) : $attribute_options);

		$xmlWriter->startElement('spec');
		$xmlWriter->writeAttribute('name', $attribute_label);
		$xmlWriter->writeCData($term);
		$xmlWriter->endElement();
	}

	$xmlWriter->endElement();
}

function delivery($xmlWriter)
{
	$xmlWriter->startElement('delivery');
	$xmlWriter->startElement('home_delivery');
	$xmlWriter->writeElement('working_days', '2');
	$xmlWriter->writeElement('price', '0.00');
	$xmlWriter->endElement();
	$xmlWriter->endElement();
}