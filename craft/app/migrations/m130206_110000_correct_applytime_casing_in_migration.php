<?php
namespace Craft;

/**
 * Craft by Pixel & Tonic
 *
 * @package   Craft
 * @author    Pixel & Tonic, Inc.
 * @copyright Copyright (c) 2013, Pixel & Tonic, Inc.
 * @license   http://buildwithcraft.com/license1.0.html Craft License
 * @link      http://buildwithcraft.com
 */

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_migrationName
 */
class m130206_110000_correct_applytime_casing_in_migration extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$migrationsTable = $this->dbConnection->schema->getTable('{{migrations}}');

		if ($migrationsTable)
		{
			if (!$migrationsTable->getColumn('applyTime') && $migrationsTable->getColumn('apply_time'))
			{
				$this->renameColumn('{{migrations}}', 'apply_time', 'applyTime');

				$this->refreshTableSchema('{{migrations}}');
				MigrationRecord::model()->refreshMetaData();
			}
			else
			{
				Craft::log('The `applyTime` column already exists in the `migrations` table.', \CLogger::LEVEL_WARNING);
			}
		}
		else
		{
			Craft::log('The `migrations` table is missing. No idea what is going on here.', \CLogger::LEVEL_ERROR);
		}
	}
}
