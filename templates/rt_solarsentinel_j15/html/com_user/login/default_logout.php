<?php // @version $Id: default_logout.php 10381 2008-06-01 03:35:53Z pasamio $
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<form action="index.php" method="post" name="login" id="login" class="form-login<?php echo $this->params->get( 'pageclass_sfx' ); ?> form-logout">

	<?php if ( $this->params->get( 'description_logout' ) || isset( $this->image ) ) : ?>
	<div class="contentdescription<?php echo $this->params->get( 'pageclass_sfx' ); ?>">
		<?php if (isset ($this->image)) :
			echo $this->image;
		endif;
		if ( $this->params->get( 'description_logout' ) ) : ?>
		<p>
			<?php echo $this->params->get('description_logout_text'); ?>
		</p>
		<?php endif;
		if (isset ($this->image)) : ?>
		<div class="wrap_image">&nbsp;</div>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<p><div class="readon-wrap1"><div class="readon1-l"></div><a class="readon-main"><span class="readon1-m"><span class="readon1-r"><input type="submit" name="Submit" class="button" value="<?php echo JText::_('Logout'); ?>" /></span></span></a></div><div class="clr"></div></p>
	<input type="hidden" name="option" value="com_user" />
	<input type="hidden" name="task" value="logout" />
	<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
</form>
