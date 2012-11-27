<?php // @version $Id: default_error.php 11299 2008-11-22 01:40:44Z ian $
defined('_JEXEC') or die('Restricted access');
?>

<h2 class="error">
	<?php echo JText::_('Error') ?>
</h2>
<div class="error">
	<p><?php echo $this->escape($this->error); ?></p>
</div>
