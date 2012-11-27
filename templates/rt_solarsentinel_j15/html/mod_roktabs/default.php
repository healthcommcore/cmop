<?php // no direct access
defined('_JEXEC') or die('Restricted access');

$document =& JFactory::getDocument();
$mod = JURI::base() . 'modules/mod_roktabs/tmpl/';

$count = count($list);

// options
$style 			= $params->get('style', 'base');
$width			= $params->get('width', 500);
$tabs			= $params->get('tabs_count', 3);
$tabs_position		= $params->get('tabs_position', 'top');
$tabs_title 		= $params->get('tabs_title', 'content'); // content | h6 | incremental
$tabs_incremental	= $params->get('tabs_incremental', 'Tab ');
$tabs_hideh6		= $params->get('tabs_hideh6', 1);
$catid			= $params->get('catid', 0);
$secid			= $params->get('secid', 1);
$duration		= $params->get('duration', 600);
$transition_type	= $params->get('transition_type', 'scrolling');
$transition_fx		= $params->get('transition_fx', 'Quad.easeInOut');
$autoplay		= $params->get('autoplay', 0);
$autoplay_delay		= $params->get('autoplay_delay', 2000);

if (intval($tabs) > $count) $tabs = $count;
else if (intval($tabs) == 0) $tabs = $count;
if (strlen($tabs_incremental) <= 0) $tabs_incremental = "Tab ";

$style_css = $mod . $style . '/roktabs.css';
if (file_exists($style_css)) $document->addStyleSheet($style_css);
if (!defined('ROKTABS_JS')) {
	$document->addScript($mod . 'roktabs.js');
	define('ROKTABS_JS',1);
}

/* IE6 Fix */
$templateStyle = $document->params->get('bodyStyle', 'dark');
if ($templateStyle == 'light' && rok_isIe(6)) $width -= 2;


$write_tabs = modRokTabsHelper::write_tabs($tabs, $tabs_position, $list, $tabs_title, $tabs_incremental, $tabs_hideh6);

?>
	<script type="text/javascript">
		RokTabsOptions.duration.push(<?php echo $duration; ?>);
		RokTabsOptions.transition.push(Fx.Transitions.<?php echo $transition_fx; ?>);
		RokTabsOptions.auto.push(<?php echo $autoplay; ?>);
		RokTabsOptions.delay.push(<?php echo $autoplay_delay; ?>);
		RokTabsOptions.type.push('<?php echo $transition_type; ?>');
	</script>
	<div class="tabs-<?php echo $tabs_position; ?>">
		<div class="roktabs-wrapper" style="width: <?php echo $width; ?>px;">
			<div class="roktabs <?php echo $style; ?>">
				<!--<div class="roktabs-arrows">
					<span class="previous">&larr;</span>
					<span class="next">&rarr;</span>
				</div>-->
				<?php 
					if ($tabs_position == 'top' || $tabs_position == 'hidden') echo $write_tabs;
				?>
				<div class="roktabs-container-tr">
					<div class="roktabs-container-tl">
						<div class="roktabs-container-br">
							<div class="roktabs-container-bl">
								<div class="roktabs-container-inner">
									<div class="roktabs-container-wrapper">
										<?php
										if ($tabs == 0) $tabs = count($list);
										for($i = 0; $i < $tabs; $i++) {
											if ($list[$i]->title != '' && $list[$i]->introtext != '') {
												echo "<div class='roktabs-tab".($i+1)."'>\n";
												echo "	<div class='wrapper'>\n";
												echo 		$list[$i]->introtext;
												echo "	</div>";
												echo "</div>\n";
											}
										}
						
										?>
									</div>
								</div>
							</div>
						</div>
					</div>	
				</div>
				<?php 
					if ($tabs_position == 'bottom') echo $write_tabs;
				?>
			</div>
		</div>
	</div>
	
<?php

?>