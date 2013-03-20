<?php

/**
 * Craft by Pixel & Tonic
 *
 * @package   Craft
 * @author    Pixel & Tonic, Inc.
 * @copyright Copyright (c) 2013, Pixel & Tonic, Inc.
 * @license   http://buildwithcraft.com/license1.0.html Craft License
 * @link      http://buildwithcraft.com
 */

$configPath = dirname(__FILE__).'/../config/console.php';

defined('CRAFT_BASE_PATH')         || define('CRAFT_BASE_PATH', str_replace('\\', '/', realpath(dirname(__FILE__).'/../../../')).'/');
defined('CRAFT_APP_PATH')          || define('CRAFT_APP_PATH',          CRAFT_BASE_PATH.'app/');
defined('CRAFT_CONFIG_PATH')       || define('CRAFT_CONFIG_PATH',       CRAFT_BASE_PATH.'config/');
defined('CRAFT_PLUGINS_PATH')      || define('CRAFT_PLUGINS_PATH',      CRAFT_BASE_PATH.'plugins/');
defined('CRAFT_STORAGE_PATH')      || define('CRAFT_STORAGE_PATH',      CRAFT_BASE_PATH.'storage/');
defined('CRAFT_TEMPLATES_PATH')    || define('CRAFT_TEMPLATES_PATH',    CRAFT_BASE_PATH.'templates/');
defined('CRAFT_TRANSLATIONS_PATH') || define('CRAFT_TRANSLATIONS_PATH', CRAFT_BASE_PATH.'translations/');

/**
 * Yii command line script file configured for Craft.
 */

// fix for fcgi
defined('STDIN') or define('STDIN', fopen('php://stdin', 'r'));

require_once dirname(__FILE__).'/../../framework/yii.php';
require_once CRAFT_APP_PATH.'Craft.php';
require_once CRAFT_APP_PATH.'Info.php';

require_once(dirname(__FILE__).'/ConsoleApp.php');

$app = Yii::createApplication('Craft\ConsoleApp', $configPath);
$app->commandRunner->addCommands(Craft\Craft::getPathOfAlias('application.etc.console.commands.*'));

$app->run();
