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
 *
 */
class RichTextFieldType extends BaseFieldType
{
	/**
	 * Returns the type of field this is.
	 *
	 * @return string
	 */
	public function getName()
	{
		return Craft::t('Rich Text');
	}

	/**
	 * Defines the settings.
	 *
	 * @access protected
	 * @return array
	 */
	protected function defineSettings()
	{
		return array(
			'minHeight' => array(AttributeType::Number, 'default' => 100, 'min' => 1),
		);
	}

	/**
	 * Returns the field's settings HTML.
	 *
	 * @return string|null
	 */
	public function getSettingsHtml()
	{
		return craft()->templates->render('_components/fieldtypes/RichText/settings', array(
			'settings' => $this->getSettings()
		));
	}

	/**
	 * Returns the content attribute config.
	 *
	 * @return mixed
	 */
	public function defineContentAttribute()
	{
		return array(AttributeType::String, 'column' => ColumnType::Text);
	}

	/**
	 * Preps the field value for use.
	 *
	 * @param mixed $value
	 * @return mixed
	 */
	public function prepValue($value)
	{
		// Prevent everyone from having to use the |raw filter when outputting RTE content
		$charset = craft()->templates->getTwig()->getCharset();
		return new \Twig_Markup($value, $charset);
	}

	/**
	 * Returns the field's input HTML.
	 *
	 * @param string $name
	 * @param mixed  $value
	 * @return string
	 */
	public function getInputHtml($name, $value)
	{
		craft()->templates->includeCssResource('lib/redactor/redactor.css');
		craft()->templates->includeJsResource('lib/redactor/redactor'.(craft()->config->get('useCompressedJs') ? '.min' : '').'.js');
		craft()->templates->includeJsResource('lib/redactor/plugins/fullscreen.js');

		$config = array(
			'buttons' => array('html','|','formatting','|','bold','italic','|','unorderedlist','orderedlist','|','link','image','video','table'),
			'plugins' => array('fullscreen'),
		);

		if ($this->getSettings()->minHeight)
		{
			$config['minHeight'] = $this->getSettings()->minHeight;
		}

		$configJson = JsonHelper::encode($config);

		craft()->templates->includeJs('$(".redactor-'.$this->model->handle.'").redactor('.$configJson.');');

		return '<textarea name="'.$name.'" class="redactor-'.$this->model->handle.'" style="display: none">'.$value.'</textarea>';
	}
}
