<?php // no direct access
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript">
<?php foreach ($list as $item): ?>
    RokStoriesImage.push('<?php echo $item->image; ?>');
<?php endforeach; ?>

window.addEvent('domready', function() {
	new RokStories('.feature-block', {
		'startElement': <?php echo $params->get("start_element", 0); ?>,
		'thumbsOpacity': <?php echo $params->get("thumbs_opacity", 0.3); ?>,
		'mousetype': '<?php echo $params->get("mouse_type", "click"); ?>',
		'autorun': <?php echo $params->get("autoplay", 0); ?>,
		'delay': <?php echo $params->get("autoplay_delay", 5000); ?>,
		'startWidth': <?php echo $params->get("start_width", 410); ?>
	});
});
</script>
<div class="feature-block">
	<div class="image-container">
		<div class="image-full"></div>
		<div class="image-small">
		    <?php foreach ($list as $item): ?>
		    <img src="<?php echo $item->thumb; ?>" class="feature-sub" alt="image" />
			<?php endforeach; ?>
		</div>
	</div>
	<div class="desc-container">
	    <?php foreach ($list as $item): ?>
	        
		<div class="description">
		    <?php if ($params->get("show_article",1)==1): ?>
				<span class="feature-title"><?php echo $item->title; ?></span>
				<span class="feature-desc"><?php echo $item->introtext; ?></span>
				<div class="clr"></div><div class="readon-wrap1"><div class="readon1-l"></div><a href="<?php echo $item->link; ?>" class="readon-main"><span class="readon1-m"><span class="readon1-r"><?php echo $params->get("readon_text","Read the Full Story"); ?></span></span></a></div><div class="clr"></div>
			<?php endif; ?>
		</div>
        <?php endforeach; ?>
	</div>
</div>