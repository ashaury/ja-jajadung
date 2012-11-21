<?php
/**
 * @version   $Id: item.php 54236 2012-07-10 22:04:13Z djamil $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */

/**
 * @var $item RokSprocket_Item
 */
?>
<li<?php echo strlen($item->custom_tags) ? ' class="'.$item->custom_tags.'"' : ''; ?> data-mosaic-item>
	<div class="sprocket-mosaic-item" data-mosaic-content>
		<?php echo $item->custom_ordering_items; ?>
		<div class="sprocket-padding">
			<div class="sprocket-mosaic-content">

				<?php if ($item->getPrimaryLink()) : ?>
				<a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>">
				<?php endif; ?>
					<?php if ($item->getPrimaryImage()) :?>
					<div class="sprocket-mosaic-image-container">
						<img src="<?php echo $item->getPrimaryImage()->getSource(); ?>" alt="" class="sprocket-mosaic-image" />
					</div>
				<?php if ($item->getPrimaryLink()) : ?>
				</a>
				<?php endif; ?>
				

				<?php if ($item->getText() || $item->getPrimaryLink()): ?>
				<div class="sprocket-mosaic-text-container">
					<div class="sprocket-mosaic-text">
						<?php echo $item->getText(); ?>
					</div>

					<?php if ($item->getPrimaryLink()) : ?>
					<a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>" class="sprocket-readmore readmore"><span><?php rc_e('READ_MORE'); ?></span></a><span class="sprocket-viewicon"><span></span></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<?php endif; ?>
		</div>

			<?php if ($parameters->get('mosaic_article_details')): ?>
			<div class="sprocket-mosaic-infos">
				<?php if ($parameters->get('mosaic_article_details') != 'date'): ?>
				By 	<span class="author"><?php echo $item->getAuthor(); ?></span>
				<?php endif; ?>
				<?php if ($parameters->get('mosaic_article_details') == 1): ?><span class="info-separator"> / </span><?php endif; ?>
				<?php if ($parameters->get('mosaic_article_details') != 'author'): ?>
				<span class="date"><?php echo $item->getDate();?></span>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<?php if ($item->getTitle()): ?>
			<h2 class="sprocket-mosaic-title<?php if ($item->getPrimaryImage() == false) echo ' sprocket-noimage'; ?>">
				<?php if ($item->getPrimaryLink()): ?><a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>"><?php endif; ?>
					<?php echo $item->getTitle();?>
				<?php if ($item->getPrimaryLink()): ?></a><?php endif; ?>
			</h2>
			<?php endif; ?>

			<?php if ($item->getPrimaryImage() == false): ?>
				<div class="sprocket-mosaic-text-container sprocket-noimage">
					<div class="sprocket-mosaic-text">
						<?php echo $item->getText(); ?>
					</div>

					<?php if ($item->getPrimaryLink()) : ?>
					<a href="<?php echo $item->getPrimaryLink()->getUrl(); ?>" class="sprocket-readmore readmore"><span><?php rc_e('READ_MORE'); ?></span></a><span class="sprocket-viewicon"><span></span></span>
					<?php endif; ?>
					<div class="clear"></div>
				</div>
				<?php endif; ?>

			<?php if (count($item->custom_tags_list)) : ?>
				<ul class="sprocket-mosaic-tags">
				<?php
					foreach($item->custom_tags_list as $key => $name){
				 		echo ' <li class="sprocket-tags-'.$key.'">'.$name.'</li>';
					}
				?>
				</ul>
				<div class="clear"></div>
			<?php endif; ?>

		</div>
	</div>
</li>
