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
 * User element type
 */
class UserElementType extends BaseElementType
{
	/**
	 * Returns the element type name.
	 *
	 * @return string
	 */
	public function getName()
	{
		return Craft::t('Users');
	}

	/**
	 * Returns the CP edit URI for a given element.
	 *
	 * @param BaseElementModel $element
	 * @return string|null
	 */
	public function getCpEditUriForElement(BaseElementModel $element)
	{
		if (Craft::hasPackage(CraftPackage::Users))
		{
			return 'users/'.$element->id;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Returns whether this element type is linkable.
	 *
	 * @return bool
	 */
	public function isLinkable()
	{
		if (Craft::hasPackage(CraftPackage::Users))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Defines any custom element criteria attributes for this element type.
	 *
	 * @return array
	 */
	public function defineCustomCriteriaAttributes()
	{
		return array(
			'groupId'       => AttributeType::Number,
			'group'         => AttributeType::Mixed,
			'username'      => AttributeType::String,
			'firstName'     => AttributeType::String,
			'lastName'      => AttributeType::String,
			'email'         => AttributeType::Email,
			'admin'         => AttributeType::Bool,
			'status'        => array(AttributeType::Enum, 'values' => array(UserStatus::Active, UserStatus::Locked, UserStatus::Suspended, UserStatus::Pending, UserStatus::Archived), 'default' => UserStatus::Active),
			'lastLoginDate' => AttributeType::DateTime,
			'order'         => array(AttributeType::String, 'default' => 'username asc')
		);
	}

	/**
	 * Returns the link settings HTML
	 *
	 * @return string|null
	 */
	public function getLinkSettingsHtml()
	{
		return craft()->templates->render('_components/elementtypes/User/linksettings', array(
			'settings' => $this->getLinkSettings()
		));
	}

	/**
	 * Modifies an entries query targeting entries of this type.
	 *
	 * @param DbCommand $query
	 * @param ElementCriteriaModel $criteria
	 * @return mixed
	 */
	public function modifyElementsQuery(DbCommand $query, ElementCriteriaModel $criteria)
	{
		Craft::requirePackage(CraftPackage::Users);

		$query
			->addSelect('users.username, users.photo, users.firstName, users.lastName, users.email, users.admin, users.status, users.lastLoginDate, users.lockoutDate')
			->join('users users', 'users.id = elements.id');
	}

	/**
	 * Populates an element model based on a query result.
	 *
	 * @param array $row
	 * @return array
	 */
	public function populateElementModel($row)
	{
		return UserModel::populateModel($row);
	}
}
