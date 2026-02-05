<?php

namespace Hoo\ProductFeeds\Presentation\Taxonomy;

use Hoo\ProductFeeds\Application\Term\{
	Enum,
	RepositoryInterface,
};
use WP_Term;

class Controller
{
	protected const COLUMN_NAME = 'product_feeds';

	public function __construct(protected readonly RepositoryInterface $repository)
	{
	}

	public function __invoke(string $taxonomy): void
	{
		add_filter("manage_edit-{$taxonomy}_columns", $this->add(...), PHP_INT_MAX, 1);
		add_filter("manage_{$taxonomy}_custom_column", $this->add2(...), PHP_INT_MAX, 3);
		add_action("{$taxonomy}_add_form_fields", $this->renderEditField2(...), PHP_INT_MAX, 1);
		add_action("{$taxonomy}_edit_form_fields", $this->renderEditField(...), PHP_INT_MAX, 2);
		add_action("create_{$taxonomy}", $this->saveField(...), PHP_INT_MAX, 1);
		add_action("edited_{$taxonomy}", $this->saveField(...), PHP_INT_MAX, 1);
	}

	protected function add(array $columns): array
	{
		$columns[self::COLUMN_NAME] = __('Product feeds', 'woocommerce-plugin-product-feeds');
		return $columns;
	}

	protected function add2(string $string, string $columnName, int $termId): string
	{
		return match ($columnName) {
			self::COLUMN_NAME => $this->repository->get($termId)->label(),
			default => $string,
		};
	}

	protected function renderEditField(WP_Term $term, string $taxonomy): void
	{
		echo $this->f($this->repository->get($term->term_id)->value);
	}

	protected function renderEditField2(string $taxonomy): void
	{
		echo $this->f('');
	}

	protected function f($value)
	{
		return
			'
			<tr class="form-field">
				<th scope="row">
					<label for="' . self::COLUMN_NAME . '">' . __('Product feeds', 'woocommerce-plugin-product-feeds') . '</label>
				</th>
				<td>
					<select name="' . self::COLUMN_NAME . '" id="' . self::COLUMN_NAME . '" class="postform" aria-describedby="' . self::COLUMN_NAME . '-description">
						<option class="level-0" value="included"' . selected($value, "included") . '>' . __('Included', 'woocommerce-plugin-product-feeds') . '</option>
						<option class="level-0" value="excluded"' . selected($value, "excluded") . '>' . __('Excluded', 'woocommerce-plugin-product-feeds') . '</option>
					</select>
					<p class="description" id="' . self::COLUMN_NAME . '-description">' . __('Product feeds description', 'woocommerce-plugin-product-feeds') . '</p>
				</td>
			</tr>
		';
	}

	protected function saveField(int $termId): void
	{
		$this->repository->set($termId, Enum::tryFrom($_POST[self::COLUMN_NAME]) ?? Enum::Included);
	}
}
