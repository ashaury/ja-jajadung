<?php
/**
 * @version   $Id$
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */
defined('_JEXEC') or die();


/**
 *
 */
class JFormFieldSignin extends JFormField
{
    /**
     * @var string
     */
    public $type = 'SignIn';

    /**
     * @return string
     */
    public function getInput()
    {
        $app = JFactory::getApplication();

        if ($message = JRequest::getVar('message', 0, 'get', 'string')) {
            $app->enqueueMessage($message);
        }

        $image = JURI::Root(true) . "/modules/mod_roktwittie/admin/images/oauth.png";

        $document = JFactory::getDocument();
        $document->addScript(JURI::root(true) . "/modules/mod_roktwittie/admin/js/oauth" . $this->_getJSVersion() . ".js");

        jimport('joomla.application.component.modeladmin');
        $app = JFactory::getApplication('administrator');
        $id  = (int)$app->getUserState('com_modules.edit.module.id');

        if (!$id) {
            return '<span id="signin-key">Save the module first</span>';
        }

        if ($id == 1) $id = (int)JRequest::getInt('id');

        $url = JURI::Root(true) . "/modules/mod_roktwittie/api.php?task=redirect&cid=" . $id;

        return '<a id="signin-key" href="' . $url . '"><img src="' . $image . '" alt="Sign in with Twitter"/></a>';
    }

    /**
     * @return string
     */
    private function _getJSVersion()
    {
        return "";
    }
}