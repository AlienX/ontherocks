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
class TemplatesController extends BaseController
{
	// Any permissions not covered in actionRender() should be handled by the templates
	public $allowAnonymous = true;

	/**
	 * Renders a template.
	 */
	public function actionRender($template, array $variables = array())
	{
		if (craft()->request->isCpRequest())
		{
			// Make sure the user has access to the CP
			craft()->userSession->requireLogin();
			craft()->userSession->requirePermission('accessCp');

			// If they're accessing a plugin's section, make sure that they have permission to do so
			$firstSeg = craft()->request->getSegment(1);
			if ($firstSeg)
			{
				$plugin = $plugin = craft()->plugins->getPlugin($firstSeg);
				if ($plugin)
				{
					craft()->userSession->requirePermission('accessPlugin-'.$plugin->getClassHandle());
				}
			}
		}

		$this->_render($template, $variables);
	}

	/**
	 * Shows the 'offline' template.
	 */
	public function actionOffline()
	{
		// If this is a site request, make sure the offline template exists
		if (craft()->request->isSiteRequest())
		{
			if (!IOHelper::fileExists(craft()->path->getSiteTemplatesPath().'offline.html'))
			{
				// Set PathService to use the CP templates path instead
				craft()->path->setTemplatesPath(craft()->path->getCpTemplatesPath());
			}
		}

		// Output the offline template
		$this->_render('offline');
	}

	/**
	 * Renders the Manual Update notification template.
	 */
	public function actionManualUpdateNotification()
	{
		$this->_render('_special/dbupdate');
	}

	/**
	 * Renders the Manual Update template.
	 */
	public function actionManualUpdate()
	{
		$this->_render('updates/_go', array(
			'handle' => craft()->request->getSegment(2)
		));
	}

	/**
	 * Renders the Breakpoint Update notification template.
	 */
	public function actionBreakpointUpdateNotification()
	{
		$this->_render('_special/breakpointupdate', array(
			'minBuild'      => CRAFT_MIN_BUILD_REQUIRED,
			'targetVersion' => CRAFT_VERSION,
			'targetBuild'   => CRAFT_BUILD
		));
	}

	/**
	 * Renders a template, sets the mime type header, etc..
	 *
	 * @access private
	 * @param string $template
	 * @param array|null $variables
	 */
	private function _render($template, $variables = array())
	{
		try
		{
			$output = $this->renderTemplate($template, $variables, true);

			// Set the Content-Type header
			$mimeType = craft()->request->getMimeType();
			header('Content-Type: '.$mimeType.'; charset=utf-8');

			if ($mimeType == 'text/html')
			{
				// Are there any head/foot nodes left in the queue?
				$headHtml = craft()->templates->getHeadHtml();
				$footHtml = craft()->templates->getFootHtml();

				if ($headHtml)
				{
					if (($endHeadPos = stripos($output, '</head>')) !== false)
					{
						$output = substr($output, 0, $endHeadPos) . $headHtml . substr($output, $endHeadPos);
					}
					else
					{
						$output .= $headHtml;
					}
				}

				if ($footHtml)
				{
					if (($endBodyPos = stripos($output, '</body>')) !== false)
					{
						$output = substr($output, 0, $endBodyPos) . $footHtml . substr($output, $endBodyPos);
					}
					else
					{
						$output .= $footHtml;
					}
				}
			}

			// Output to the browser!
			echo $output;

			// End the request
			craft()->end();
		}
		catch (TemplateLoaderException $e)
		{
			if ($e->template == $template)
			{
				throw new HttpException(404);
			}
			else
			{
				throw $e;
			}
		}
	}
}
