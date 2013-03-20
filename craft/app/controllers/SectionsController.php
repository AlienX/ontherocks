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
 * Handles section management tasks
 */
class SectionsController extends BaseController
{
	/**
	 * Saves a section
	 */
	public function actionSaveSection()
	{
		$this->requirePostRequest();

		$section = new SectionModel();

		// Set the simple stuff
		$section->id         = craft()->request->getPost('sectionId');
		$section->name       = craft()->request->getPost('name');
		$section->handle     = craft()->request->getPost('handle');
		$section->titleLabel = craft()->request->getPost('titleLabel');
		$section->hasUrls    = (bool)craft()->request->getPost('hasUrls');
		$section->template   = craft()->request->getPost('template');

		// Set the locales and URL formats
		$locales = array();
		$urlFormats = craft()->request->getPost('urlFormat');

		if (Craft::hasPackage(CraftPackage::Localize))
		{
			$localeIds = craft()->request->getPost('locales');
		}
		else
		{
			$primaryLocaleId = craft()->i18n->getPrimarySiteLocale()->getId();
			$localeIds = array($primaryLocaleId);
		}

		foreach ($localeIds as $localeId)
		{
			$locales[$localeId] = SectionLocaleModel::populateModel(array(
				'locale'    => $localeId,
				'urlFormat' => (isset($urlFormats[$localeId]) ? $urlFormats[$localeId] : null),
			));
		}

		$section->setLocales($locales);

		// Set the field layout
		$fieldLayout = craft()->fields->assembleLayoutFromPost();
		$fieldLayout->type = ElementType::Entry;
		$section->setFieldLayout($fieldLayout);

		// Save it
		if (craft()->sections->saveSection($section))
		{
			craft()->userSession->setNotice(Craft::t('Section saved.'));

			$this->redirectToPostedUrl(array(
				'sectionId' => $section->id
			));
		}
		else
		{
			craft()->userSession->setError(Craft::t('Couldn’t save section.'));
		}

		// Send the section back to the template
		craft()->urlManager->setRouteVariables(array(
			'section' => $section
		));
	}

	/**
	 * Deletes a section.
	 */
	public function actionDeleteSection()
	{
		$this->requirePostRequest();
		$this->requireAjaxRequest();

		$sectionId = craft()->request->getRequiredPost('id');

		craft()->sections->deleteSectionById($sectionId);
		$this->returnJson(array('success' => true));
	}
}
