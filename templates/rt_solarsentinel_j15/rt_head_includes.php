<?php

// This information has been pulled out of index.php to make the template more readible.
//
// This data goes between the <head></head> tags of the template
// 
// 

$this->addStylesheet($this->baseurl."/templates/".$this->template."/css/template.css");
$this->addStylesheet($this->baseurl."/templates/".$this->template."/css/header-".$header_style.".css");
$this->addStylesheet($this->baseurl."/templates/".$this->template."/css/body-".$body_style.".css");
$this->addStylesheet($this->baseurl."/templates/".$this->template."/css/footer-".$footer_style.".css");
$this->addStylesheet($this->baseurl."/templates/".$this->template."/css/modules.css");
$this->addStylesheet($this->baseurl."/templates/".$this->template."/css/typography.css");
$this->addStylesheet($this->baseurl."/templates/".$this->template."/css/print.css", $type="text/css", $media = "print");
$this->addStylesheet($this->baseurl."/templates/system/css/system.css");
$this->addStylesheet($this->baseurl."/templates/system/css/general.css");
if($mtype=="moomenu" or $mtype=="suckerfish") :
    $this->addStylesheet($this->baseurl."/templates/".$this->template."/css/rokmoomenu.css");
endif;
$inlinestyle = "
	div.wrapper { ".$template_width."padding:0;}
	#inset-block-left { width:".$leftinset_width."px;padding:0;}
	#inset-block-right { width:".$rightinset_width."px;padding:0;}
	#maincontent-block { margin-right:".$rightinset_width."px;margin-left:".$leftinset_width."px;}
	a, .grey .side-mod a, .componentheading span, .roktabs-links li.active {color: ".$primary_color.";}";
$this->addStyleDeclaration($inlinestyle);
?>
<?php if (rok_isIe()) :?>
<!--[if IE 7]>
<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template?>/css/template_ie7.css" rel="stylesheet" type="text/css" />	
<![endif]-->	
<?php endif; ?>
<?php if (rok_isIe(6)) :?>
<!--[if lte IE 6]>
<link href="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template?>/css/template_ie6.css" rel="stylesheet" type="text/css" />
<script src="<?php echo $this->baseurl; ?>/templates/<?php echo $this->template?>/js/DD_belatedPNG.js"></script>
<script>
    DD_belatedPNG.fix('.png');
</script>
<![endif]-->
<?php endif; ?>
<?php 
if($enable_fontspans=="true") :
    $this->addScript($this->baseurl."/templates/".$this->template."/js/rokfonts.js");
    $rokfonts = 
    "window.addEvent('domready', function() {
		var modules = ['side-mod','module','moduletable','component-header'];
		var header = ['h3','h1'];
		RokBuildSpans(modules, header);
	});";
    $this->addScriptDeclaration($rokfonts);
endif;
if(rok_isIe(6) and $enable_ie6warn=="true" and $js_compatibility=="false") : 
    $this->addScript($this->baseurl."/templates/".$this->template."/js/rokie6warn.js");
endif;
if($clientside_date == "true" and $js_compatibility=="false") :
    $this->addScript($this->baseurl."/templates/".$this->template."/js/rokdate.js");
endif; 
$this->addScript($this->baseurl."/templates/".$this->template."/js/rokutils.js");
if($enable_inputstyle == "true" and $js_compatibility=="false") :
    $this->addScript($this->baseurl."/templates/".$this->template."/js/rokutils.inputs.js");
	$exclusionList = "InputsExclusion.push($inputs_exclusion)";
	$this->addScriptDeclaration($exclusionList);
endif;
if($mtype=="moomenu" and $js_compatibility=="false") :
    $this->addScript($this->baseurl."/templates/".$this->template."/js/rokmoomenu.js");
    $this->addScript($this->baseurl."/templates/".$this->template."/js/mootools.bgiframe.js");
    $mooinit =
    "window.addEvent('domready', function() {
    	new Rokmoomenu(".'$E'."('ul.menutop '), {
    		bgiframe: ".$moo_bgiframe.",
    		delay: ".$moo_delay.",
    		verhor: true,
    		animate: {
    			props: ['height'],
    			opts: {
    				duration: ".$moo_duration.",
    				fps: ".$moo_fps.",
    				transition: Fx.Transitions.".$moo_transition."
    			}
    		},
    		bg: {
    			enabled: ".$moo_bg_enabled.",
    			overEffect: {
    				duration: ".$moo_bg_over_duration.",
    				transition: Fx.Transitions.".$moo_bg_over_transition."
    			},
    			outEffect: {
    				duration: ".$moo_bg_out_duration.",
    				transition: Fx.Transitions.".$moo_bg_out_transition."
    			}
    		},
    		submenus: {
    			enabled: ".$moo_sub_enabled.",
    			opacity: ".$moo_sub_opacity.",
    			overEffect: {
    				duration: ".$moo_sub_over_duration.",
    				transition: Fx.Transitions.".$moo_sub_over_transition."
    			},
    			outEffect: {
    				duration: ".$moo_sub_out_duration.",
    				transition: Fx.Transitions.".$moo_sub_out_transition."
    			},
    			offsets: {
    				top: ".$moo_sub_offsets_top.",
    				right: ".$moo_sub_offsets_right.",
    				bottom: ".$moo_sub_offsets_bottom.",
    				left: ".$moo_sub_offsets_left."
    			}
    		}
    	});
    });";
    $this->addScriptDeclaration($mooinit);
endif;
if((rok_isIe(6) or rok_isIe(7)) and ($mtype=="suckerfish" or $mtype=="splitmenu")) :
    $this->addScript($this->baseurl."/templates/".$this->template."/js/ie_suckerfish.js");

endif; ?>
