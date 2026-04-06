<?php

namespace Hoo\WooCommercePlugin\LtProductFeeds\Infrastructure\Database\Migration\CreateFeedAccessTable;

use Hoo\WordPressPluginFramework\Database\Migration\MigrationInterface;
use wpdb;

readonly class Migration implements MigrationInterface
{
	public function __construct(
		protected wpdb $wpdb,
	) {
	}

	public function up(): void
	{
		$sql = file_get_contents(__DIR__ . '/Query.sql');

		$sql = str_replace(':table', $this->wpdb->prefix . 'lt_feed_access', $sql);

		$this->wpdb->query($sql);
	}
}
