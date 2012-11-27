<?php
/**
* @package mod_rokajaxsearch
* @copyright	Copyright (C) 2008 RocketTheme. All rights reserved.
* @license		http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL, see LICENSE.php
* RokAjaxSearch is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*
* Inspired on PixSearch Joomla! module by Henrik Hussfelt <henrik@pixpro.net>
*/

defined('_JEXEC') or die('Restricted access');

$websearch = ($params->get('websearch', 0) && $params->get('websearch_api') != '') ? 1 : 0;
?>
<form name="rokajaxsearch" id="rokajaxsearch" action="<?php echo JURI::Base()?>" method="get">
<div class="rokajaxsearch<?php echo $params->get('moduleclass_sfx'); ?>">
	<input id="roksearch_search_str" name="searchword" type="text" class="inputbox" value="<?php echo JText::_('SEARCH'); ?>" />
	<input type="hidden" name="searchphrase" value="<?php echo $params->get("searchphrase")?>"/>
	<input type="hidden" name="limit" value="" />
	<input type="hidden" name="ordering" value="<?php echo $params->get("ordering")?>" />
	<input type="hidden" name="view" value="search" />
	<input type="hidden" name="Itemid" value="99999999" />
	<input type="hidden" name="option" value="com_search" />

	<?php if ($websearch): ?>
		<div class="search_options">
			<label style="float: left; margin-right: 8px">
					<input type="radio" name="search_option[]" value="local" checked="checked" /><?php echo JText::_('LOCAL_SEARCH'); ?>
			</label>
			<label style="float: left;">
				<input type="radio" name="search_option[]" value="web" /><?php echo JText::_('WEB_SEARCH'); ?>
			</label>
		</div>
	<?php endif; ?>

	<div id="roksearch_results"></div>
	<script type="text/javascript">
	window.addEvent((window.webkit) ? 'load' : 'domready', function() {
		window.rokajaxsearch = new RokAjaxSearch({
			'results': '<?php echo JText::_('RESULTS'); ?>',
			'close': '',
			'websearch': <?php echo $websearch; ?>,
			'search': '<?php echo JText::_('SEARCH'); ?>',
			'readmore': '<?php echo JText::_('READMORE'); ?>',
			'noresults': '<?php echo JText::_('NORESULTS'); ?>',
			'advsearch': '<?php echo JText::_('ADVSEARCH'); ?>',
			'page': '<?php echo JText::_('PAGE'); ?>',
			'page_of': '<?php echo JText::_('PAGE_OF'); ?>',
			'searchlink': '<?php echo JURI::Base().htmlentities($params->get('search_page')); ?>',
			'advsearchlink': '<?php echo JURI::Base().htmlentities($params->get('adv_search_page')); ?>',
			'uribase': '<?php echo JURI::Base()?>',
			'limit': '<?php echo $params->get('limit', '10'); ?>',
			'perpage': '<?php echo $params->get('perpage', '3'); ?>',
			'ordering': '<?php echo $params->get('ordering', 'newest'); ?>',
			'phrase': '<?php echo $params->get('searchphrase', 'any'); ?>',
			'hidedivs': '<?php echo $params->get('hide_divs', ''); ?>',
			'includelink': <?php echo $params->get('include_link', 1); ?>,
			'viewall': '<?php echo JText::_('VIEWALL'); ?>',
			'estimated': '<?php echo JText::_('ESTIMATED'); ?>',
			'showestimated': <?php echo $params->get('show_estimated', 1); ?>,
			'showpagination': <?php echo $params->get('show_pagination', 1); ?>,
			'showcategory': <?php echo $params->get('include_category', 1); ?>,
			'showreadmore': <?php echo $params->get('show_readmore', 1); ?>,
			'showdescription': <?php echo $params->get('show_description', 1); ?>
		});
	});
	</script>
</div>
<div id="rokajaxsearch_tmp" style="visibility:hidden;display:none;"></div>
</form>