<?php
if (!defined('PRODUCT_FEEDS')) {
	die();
}
?>

<tr class="form-field">
	<th scope="row">
		<label for="product_feeds">
			<?= esc_html__('Product feeds', 'woocommerce-plugin-product-feeds'); ?>
		</label>
	</th>
	<td>
		<select name="product_feeds" id="product_feeds" class="postform" aria-describedby="product_feeds-description">
			<?php foreach ($options as $option): ?>
				<option class="level-0" value="<?= esc_attr($option['value']); ?>">
					<?= esc_html($option['label']); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<p class="description" id="product_feeds-description">
			<?= esc_html__('Product feeds', 'woocommerce-plugin-product-feeds'); ?>
		</p>
	</td>
</tr>