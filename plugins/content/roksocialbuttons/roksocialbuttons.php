<?php
/**
 * @version   $Id: roksocialbuttons.php 48519 2012-02-03 23:18:52Z rhuk $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 */
class plgContentRokSocialButtons extends JPlugin
{


    /**
     * @param $context
     * @param $row
     * @param $params
     * @param $limitstart
     */
    public function onContentAfterTitle($context, &$row, &$params, $page = 0)
    {
        $document   =& JFactory::getDocument();
        $app        =& JFactory::getApplication();
        $template   = $app->getTemplate();

        if ($app->isAdmin()) return;

        // Don't run this plugin when the content is being indexed
        if ($context == 'com_finder.indexer') {
            return true;
        }

        // get the context of this row
        if (!isset($row->text) && isset($row->introtext)) {
            $text = $row->introtext;
        } else {
            $text = $row->text;
        }

        // simple performance check to determine whether bot should process further
        if ( JString::strpos( $text, 'socialbuttons' ) === false ) {
            return true;
        }

        $regex = '/\{socialbuttons(.*)\}/i';
        $matches = array();
        preg_match_all($regex, $text, $matches, PREG_SET_ORDER);

        // we have matches, so let's do stuff
        if (sizeof($matches) > 0) {

            if (!defined("ROKSOCIALBUTTONS")) {
                define("ROKSOCIALBUTTONS",1);
                $script = 'http://s7.addthis.com/js/250/addthis_widget.js#pubid='.$this->params->get('addthis-id','');

                // use template stylesheet if it exists
                $builtin_path = '/plugins/content/roksocialbuttons/assets/roksocialbuttons.css';
                $template_path = '/templates/'.$template.'/html/plg_roksocialbuttons/roksocialbuttons.css';
                $template_stylesheet = JPATH_SITE.$template_path;

                if (file_exists($template_stylesheet)) $stylesheet = JURI::base(true).$template_path;
                else $stylesheet = JURI::base(true).$builtin_path;

                //get css from template or plugin
                $document->addStyleSheet($stylesheet);
                $document->addScript($script);
            }

            $r_id = $row->id;
            $r_catid = $row->catid;
            $r_slug = $row->slug;
            $r_alias = $row->alias;

               //url
            $baseurl = (!empty($_SERVER['HTTPS'])) ? "https://" . $_SERVER['HTTP_HOST'] : "http://" . $_SERVER['HTTP_HOST'];
            if ($_SERVER['SERVER_PORT'] != "80") $baseurl .= ":" . $_SERVER['SERVER_PORT'];
            $this->host = $_SERVER['HTTP_HOST'];
            $this->path = JRoute::_(ContentHelperRoute::getArticleRoute($r_slug, $r_catid));
            $this->url = $baseurl . $this->path;


            $script = '<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid='.$this->params->get('addthis-id',1).'"></script>';

            foreach ($matches as $match){

                $passed_param = $match[1];
                $module_params = array();

                if (isset($passed_param)) {
                    $param_match = array();
                    preg_match_all('/((\w+)\="(.*)")/i', $passed_param, $param_match, PREG_SET_ORDER);
                    foreach ($param_match as $pmatch) {
                        $module_params[$pmatch[2]] = $pmatch[3];
                    }
                }

                $class = isset($module_params['class']) ? $module_params['class'] : '';

                $code = '
                    <div class="roksocialbuttons addthis_toolbox '.$class.' '.$this->params->get('extra_class','').'"
                       addthis:url="'.$this->url.'"
                       addthis:title="'.$row->title.'">
                    <div class="custom_images">';
                if (trim($this->params->get('prepend_text','')) != "") {
                    $code .= '<h4>'.$this->params->get('prepend_text').'</h4>';
                }
                if ($this->params->get('enable_twitter',1)) {
                    $code .= '<a class="addthis_button_twitter"><span></span></a>';
                }
                if ($this->params->get('enable_facebook',1)) {
                    $code .= '<a class="addthis_button_facebook"><span></span></a>';
                }
                if ($this->params->get('enable_google',1)) {
                    $code .= '<a class="addthis_button_google"><span></span></a>';
                }
                $code .= '
                    </div>
                    </div>';

                $text = preg_replace($regex, $code, $text, 1);
            }

            $this->_setContentText($row,$text);

        }

    }

    protected function _setContentText(&$row,$text)
    {
        if (!isset($row->text) && isset($row->introtext)) {
            $row->introtext = $text;
        } else {
            $row->text = $text;
        }
        return;
    }

}
