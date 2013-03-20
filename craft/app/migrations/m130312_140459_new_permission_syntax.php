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
class m130312_140459_new_permission_syntax extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		$this->execute('UPDATE {{userpermissions}} SET `name` = REPLACE(`name`, "insection", ":");');
		$this->execute('UPDATE {{userpermissions}} SET `name` = REPLACE(`name`, "editglobalset", "editglobalset:");');
		$this->execute('UPDATE {{userpermissions}} SET `name` = REPLACE(`name`, "viewassetsource", "viewassetsource:");');
		return true;
	}
}
