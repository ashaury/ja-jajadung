<?php
/**
* @version   $Id: orderedbody_mainbody.php 2068 2012-07-31 22:11:16Z djamil $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/
defined('JPATH_BASE') or die();

gantry_import('core.gantrylayout');

/**
 *
 * @package gantry
 * @subpackage html.layouts
 */
class GantryLayoutOrderedBody_MainBody extends GantryLayout {
    var $render_params = array(
        'schema'        =>  null,
        'pushPull'      =>  null,
        'classKey'      =>  null,
        'sidebars'      =>  array(),
        'contentTop'    =>  null,
        'contentBottom' =>  null,
		'extraClass' => ''
    );
    function render($params = array()){
       
	    global $gantry;

        $fparams = $this-> _getParams($params);

        // logic to determine if the component should be displayed
		$display_mainbody = !($gantry->get("mainbody-enabled",true)==false && JRequest::getVar('view') == 'featured');
        $display_component = !($gantry->get("component-enabled",true)==false && (JRequest::getVar('option') == 'com_content' && JRequest::getVar('view') == 'featured'));
        $positions = array_keys($fparams->sidebars);
        $sbcount = 0;
        ob_start();

		$mbClasses = trim("rt-grid-" . trim($fparams->schema['mb'] . " " . $fparams->pushPull[0] . " " . $fparams->extraClass));
		$mbClasses = preg_replace('/\s\s+/', ' ', $mbClasses);

// XHTML LAYOUT
?>
<?php if ($display_mainbody) : ?>
<div id="rt-main" class="<?php echo $fparams->classKey; ?>">
    <div class="rt-mainsection">
        <div class="rt-mainrow">
            <?php foreach($fparams->schema as $position => $value): ?>
                <?php if ($position != 'mb'): ?>
                    <?php echo $fparams->sidebars[$positions[$sbcount]]; ?>
                    <?php $sbcount++; ?>
                <?php elseif($position == 'mb'): ?>
                    <div class="<?php echo $mbClasses; ?>">
                        <?php if (isset($fparams->contentTop)) : ?>
                        <div id="rt-content-top">
                            <div class="rt-section-surround">
                                <div class="rt-row-surround">
                                    <?php echo $fparams->contentTop; ?>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <?php endif; ?>
                        <?php if ($display_component) : ?>
                    	<div class="rt-block component-block">
            				<div class="component-content">
            					<jdoc:include type="component" />
                                <div class="clear"></div>
            				</div>
                    		<div class="clear"></div>
                    	</div>
                        <?php endif; ?>
                        <?php if (isset($fparams->contentBottom)) : ?>
                        <div id="rt-content-bottom">
                            <div class="rt-section-surround">
                                <div class="rt-row-surround">
                                    <?php echo $fparams->contentBottom; ?>
                                </div>
                            </div>
                        </div>
                        <?php else: ?>
                        <div id="rt-content-bottom-fake">
                            <div class="rt-grid-fake">
                                <div class="rt-block rt-content-bottom-dummy"></div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
            <div class="clear"></div>
        </div>
    </div>
</div>
<?php endif; ?>
<?php
        return ob_get_clean();
    }
}
