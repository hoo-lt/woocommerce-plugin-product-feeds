<?php
if (!defined('PRODUCT_FEEDS')) {
	die();
}
?>

<tr class="form-field">
	<th scope="row">
		<label for="product_feeds">
			<?= esc_html__('Product feeds', 'woocommerce-product-feeds'); ?>
		</label>
	</th>
	<td>
		<select name="product_feeds" id="product_feeds" class="postform" aria-describedby="product_feeds-description">
			<?php foreach ($labels as $label): ?>
				<option class="level-0" value="<?= esc_attr($label['value']); ?>" <?php selected($value, $label['value']); ?>>
					<?= esc_html($label['label']); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<p class="description" id="product_feeds-description">
			<?= esc_html__('Product feeds', 'woocommerce-product-feeds'); ?>
		</p>
	</td>
</tr>