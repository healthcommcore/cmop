<?php // @version $Id: confirm.php 10822 2008-08-27 17:16:00Z tcp $
defined('_JEXEC') or die('Restricted access');
?>

<div class="component-header"><div class="componentheading"><?php echo JText::_('Confirm your Account'); ?></div></div>

<form action="index.php?option=com_user&amp;task=confirmreset" method="post" class="josForm form-validate">
	<table cellpadding="0" cellspacing="0" border="0" width="100%" class="contentpane">
		<tr>
			<td colspan="2" height="40">
				<p><?php echo JText::_('RESET_PASSWORD_CONFIRM_DESCRIPTION'); ?></p>
			</td>
		</tr>
		<tr>
			<td height="40">
				<label for="token" class="hasTip" title="<?php echo JText::_('RESET_PASSWORD_TOKEN_TIP_TITLE'); ?>::<?php echo JText::_('RESET_PASSWORD_TOKEN_TIP_TEXT'); ?>"><?php echo JText::_('Token'); ?>:</label>
			</td>
			<td>
				<input id="token" name="token" type="text" class="required" size="36" />
			</td>
		</tr>
	</table>

	<div class="readon-wrap1"><div class="readon1-l"></div><a class="readon-main"><span class="readon1-m"><span class="readon1-r"><input type="submit" class="button validate" value="<?php echo JText::_('Submit'); ?>" /></span></span></a></div>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>