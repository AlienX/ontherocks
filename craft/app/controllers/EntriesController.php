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
 * Handles entry tasks
 */
class EntriesController extends BaseController
{
	/**
	 * Saves an entry.
	 */
	public function actionSaveEntry()
	{
		$this->requirePostRequest();

		$entry = new EntryModel();

		$entry->sectionId  = craft()->request->getRequiredPost('sectionId');
		$entry->locale     = craft()->request->getPost('locale', craft()->i18n->getPrimarySiteLocale()->getId());
		$entry->id         = craft()->request->getPost('entryId');
		$entry->authorId   = craft()->request->getPost('author', craft()->userSession->getUser()->id);
		$entry->title      = craft()->request->getPost('title');
		$entry->slug       = craft()->request->getPost('slug');
		$entry->postDate   = $this->getDateFromPost('postDate');
		$entry->expiryDate = $this->getDateFromPost('expiryDate');
		$entry->enabled    = craft()->request->getPost('enabled');
		$entry->tags       = craft()->request->getPost('tags');

		$fields = craft()->request->getPost('fields');
		$entry->setContent($fields);

		if (craft()->entries->saveEntry($entry))
		{
			if (craft()->request->isAjaxRequest())
			{
				$return['success']   = true;
				$return['entry']     = $entry->getAttributes();
				$return['cpEditUrl'] = $entry->getCpEditUrl();
				$return['author']    = $entry->getAuthor()->getAttributes();
				$return['postDate']  = $entry->postDate->w3cDate();

				$this->returnJson($return);
			}
			else
			{
				craft()->userSession->setNotice(Craft::t('Entry saved.'));

				$this->redirectToPostedUrl(array(
					'entryId'   => $entry->id,
					'slug'      => $entry->slug,
					'url'       => $entry->getUrl(),
					'cpEditUrl' => $entry->getCpEditUrl(),
				));
			}
		}
		else
		{
			if (craft()->request->isAjaxRequest())
			{
				$this->returnJson(array(
					'errors' => $entry->getErrors(),
				));
			}
			else
			{
				craft()->userSession->setError(Craft::t('Couldn’t save entry.'));

				// Send the entry back to the template
				craft()->urlManager->setRouteVariables(array(
					'entry' => $entry
				));
			}
		}
	}

	/**
	 * Deletes an entry.
	 */
	public function actionDeleteEntry()
	{
		$this->requirePostRequest();
		$this->requireAjaxRequest();

		$entryId = craft()->request->getRequiredPost('id');

		craft()->elements->deleteElementById($entryId);
		$this->returnJson(array('success' => true));
	}
}
