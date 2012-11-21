<?php
/**
* @version   $Id: index.php 4107 2012-10-03 20:18:17Z arifin $
* @author    RocketTheme http://www.rockettheme.com
* @copyright Copyright (C) 2007 - 2012 RocketTheme, LLC
* @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
*
* Gantry uses the Joomla Framework (http://www.joomla.org), a GNU/GPLv2 content management system
*
*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );

// load and inititialize gantry class
require_once('lib/gantry/gantry.php');
$gantry->init();

function isBrowserCapable(){
	global $gantry;

	$browser = $gantry->browser;

	// ie.
	if ($browser->name == 'ie' && $browser->version < 8) return false;

	return true;
}
// get the current preset
$gpreset = str_replace(' ','',strtolower($gantry->get('name')));

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $gantry->language; ?>" lang="<?php echo $gantry->language;?>" >
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php
		$gantry->displayHead();
		$gantry->addScripts(array('rokmediaqueries.js', 'modules-height.js'));

		if ($gantry->get('loadtransition') && isBrowserCapable()){
			$gantry->addScript('load-transition.js');
			$hidden = ' class="rt-hidden"';
		} else {
			$hidden = '';
		}

	?>
</head>
	<body <?php echo $gantry->displayBodyTag(); ?>>
		<div id="rt-page-surround">
			<?php /** Begin Drawer **/ if ($gantry->countModules('drawer')) : ?>
			<div id="rt-drawer">
				<div class="rt-container">
					<?php echo $gantry->displayModules('drawer','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Drawer **/ endif; ?>
			<?php /** Begin Top **/ if ($gantry->countModules('top')) : ?>
			<div id="rt-top">
				<div class="rt-container">
					<div class="rt-section-surround">
						<div class="rt-row-surround">
							<?php echo $gantry->displayModules('top','standard','standard'); ?>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
			<?php /** End Top **/ endif; ?>
			<?php /** Begin Header **/ if ($gantry->countModules('header')) : ?>
			<div id="rt-header" class="dark-block">
				<div class="rt-headerborder"></div>
				<div class="rt-container">
					<?php echo $gantry->displayModules('header','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Header **/ endif; ?>
			<?php /** Begin Feature **/ if ($gantry->countModules('feature')) : ?>
			<div id="rt-feature">
				<div class="rt-container">
					<?php echo $gantry->displayModules('feature','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Feature **/ endif; ?>
			<div class="rt-container">
				<div id="rt-transition"<?php echo $hidden; ?>>
					<?php /** Begin Showcase **/ if ($gantry->countModules('showcase')) : ?>
					<div id="rt-showcase">
						<div class="rt-section-surround">
							<div class="rt-row-surround">
								<?php echo $gantry->displayModules('showcase','standard','standard'); ?>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<?php /** End Showcase **/ endif; ?>
					<?php /** Begin Utility **/ if ($gantry->countModules('utility')) : ?>
					<div id="rt-utility">
						<div class="rt-section-surround">
							<div class="rt-row-surround">
								<?php echo $gantry->displayModules('utility','standard','standard'); ?>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<?php /** End Utility **/ endif; ?>
					<?php /** Begin Main Top **/ if ($gantry->countModules('maintop')) : ?>
					<div id="rt-maintop">
						<div class="rt-section-surround">
							<div class="rt-row-surround">
								<?php echo $gantry->displayModules('maintop','standard','standard'); ?>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<?php /** End Main Top **/ endif; ?>
					<?php /** Begin Breadcrumbs **/ if ($gantry->countModules('breadcrumb')) : ?>
					<div id="rt-breadcrumbs">
						<?php echo $gantry->displayModules('breadcrumb','basic','breadcrumbs'); ?>
						<div class="clear"></div>
					</div>
					<?php /** End Breadcrumbs **/ endif; ?>
					<?php /** Begin Main Body **/ ?>
				    <?php echo $gantry->displayOrderedMainbody('mainbody','sidebar','standard','standard','standard','standard','standard'); ?>
					<?php /** End Main Body **/ ?>
					<?php /** Begin Main Bottom **/ if ($gantry->countModules('mainbottom')) : ?>
					<div id="rt-mainbottom">
						<div class="rt-section-surround">
							<div class="rt-row-surround">
								<?php echo $gantry->displayModules('mainbottom','standard','standard'); ?>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<?php /** End Main Bottom **/ endif; ?>
					<?php /** Begin Extension **/ if ($gantry->countModules('extension')) : ?>
					<div id="rt-extension">
						<div class="rt-section-surround">
							<div class="rt-row-surround">
								<?php echo $gantry->displayModules('extension','standard','standard'); ?>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<?php /** End Extension **/ endif; ?>
					<?php /** Begin Social **/ if ($gantry->countModules('social')) : ?>
					<?php echo $gantry->displayModules('social','basic','basic'); ?>
					<?php /** End Social **/ endif; ?>
					<?php /** Begin Bottom **/ if ($gantry->countModules('bottom')) : ?>
					<div id="rt-bottom">
						<div class="rt-section-surround">
							<div class="rt-row-surround">
								<?php echo $gantry->displayModules('bottom','standard','standard'); ?>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					<?php /** End Bottom **/ endif; ?>
				</div>
			</div>
			<?php /** Begin Footer **/ if ($gantry->countModules('footer')) : ?>
			<div id="rt-footer" class="dark-block">
				<div class="rt-container">
					<?php echo $gantry->displayModules('footer','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Footer **/ endif; ?>
			<?php /** Begin Copyright **/ if ($gantry->countModules('copyright')) : ?>
			<div id="rt-copyright">
				<div class="rt-container">
					<?php echo $gantry->displayModules('copyright','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Copyright **/ endif; ?>
			<?php /** Begin Debug **/ if ($gantry->countModules('debug')) : ?>
			<div id="rt-debug">
				<div class="rt-container">
					<?php echo $gantry->displayModules('debug','standard','standard'); ?>
					<div class="clear"></div>
				</div>
			</div>
			<?php /** End Debug **/ endif; ?>
			<?php /** Begin Popups **/
			echo $gantry->displayModules('popup','popup','popup');
			echo $gantry->displayModules('login','login','popup');
			/** End Popup s**/ ?>
			<?php /** Begin Analytics **/ if ($gantry->countModules('analytics')) : ?>
			<?php echo $gantry->displayModules('analytics','basic','basic'); ?>
			<?php /** End Analytics **/ endif; ?>
		</div>
	</body>
</html>
<?php
$gantry->finalize();

?>
