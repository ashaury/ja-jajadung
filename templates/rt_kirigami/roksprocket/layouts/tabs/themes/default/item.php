<?php
/**
 * @version   $Id: item.php 2031 2012-07-30 23:37:45Z josh $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * @var $item RokSprocket_Item
 * @var $parameters RokCommon_Registry
 */
?>
<div class="sprocket-tabs-panel" data-tabs-panel>
	<?php echo $item->getText(); ?>
	<?php if ($item->getPrimaryLink()) : ?>
	<a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>" class="readon"><span><?php rc_e('READ_MORE'); ?></span></a>
	<?php endif; ?>
	<div class="clear"></div>
</div>
