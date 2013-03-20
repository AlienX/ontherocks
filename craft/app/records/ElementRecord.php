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
 * Element record class
 */
class ElementRecord extends BaseRecord
{
	/**
	 * @return string
	 */
	public function getTableName()
	{
		return 'elements';
	}

	/**
	 * @access protected
	 * @return array
	 */
	protected function defineAttributes()
	{
		return array(
			'type'     => array(AttributeType::ClassName, 'required' => true),
			'enabled'  => array(AttributeType::Bool, 'default' => true),
			'archived' => array(AttributeType::Bool, 'default' => false),
		);
	}

	/**
	 * @return array
	 */
	public function defineRelations()
	{
		return array(
			'i18n'    => array(static::HAS_ONE, 'ElementLocaleRecord', 'elementId', 'condition' => 'i18n.locale=:locale', 'params' => array(':locale' => craft()->language)),
			'content' => array(static::HAS_ONE, 'ContentRecord', 'elementId', 'condition' => 'content.locale=:locale', 'params' => array(':locale' => craft()->language)),
		);
	}

	/**
	 * @return array
	 */
	public function defineIndexes()
	{
		return array(
			array('columns' => array('type')),
			array('columns' => array('enabled')),
			array('columns' => array('archived')),
		);
	}
}
