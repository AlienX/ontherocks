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
class m130305_000006_transformations_to_transforms extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$assetTransformationsTable = $this->dbConnection->schema->getTable('{{assettransformations}}');
		$assetTransformsTable = $this->dbConnection->schema->getTable('{{assettransforms}}');

		if ($assetTransformationsTable && !$assetTransformsTable)
		{
			$this->dbConnection->createCommand()->renameTable('assettransformations', 'assettransforms');
			Craft::log('Successfully renamed `assettransformations` to `assettransforms`.', \CLogger::LEVEL_INFO);
		}
		else
		{
			Craft::log('Tried to rename `assettransformations` to `assettransforms`, but `assettransforms` already exists.', \CLogger::LEVEL_WARNING);
		}

		return true;
	}
}
