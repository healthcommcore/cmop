<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted index access' );
define( 'YOURBASEPATH', dirname(__FILE__) );
require( YOURBASEPATH.DS."styles.php");
require( YOURBASEPATH.DS."rt_styleswitcher.php");
JHTML::_( 'behavior.mootools' );
global $template_real_width, $leftcolumn_width, $rightcolumn_width, $tstyle, $body_style;
global $js_compatibility, $menu_rows_per_column, $menu_columns, $menu_multicollevel;
global $header_style, $body_style, $bg_style, $footer_style, $primary_color;

$live_site        		= $mainframe->getCfg('live_site');
$template_path 			= $this->baseurl . '/templates/' .  $this->template;
$preset_style 			= $this->params->get("presetStyle", "style7");

$header_style 			= $this->params->get("headerStyle", "blue");
$body_style 			= $this->params->get("bodyStyle", "white");
$bg_style 				= $this->params->get("bgStyle", "bg-white");
$footer_style 			= $this->params->get("footerStyle", "grey");
$primary_color          = $this->params->get("primaryColor", "#c00" );

$frontpage_component    = $this->params->get("enableFrontpage", "show");
$enable_ie6warn         = ($this->params->get("enableIe6warn", 0)  == 0)?"false":"true";
$font_family            = $this->params->get("fontFamily", "solarsentinel");
$enable_fontspans       = ($this->params->get("enableFontspans", 1)  == 0)?"false":"true";
$enable_inputstyle		= ($this->params->get("enableInputstyle", 1) == 0)?"false":"true";
$inputs_exclusion		= $this->params->get("inputsExclusion", "'.content_vote'");
$template_width 		= $this->params->get("templateWidth", "959");
$leftcolumn_width		= $this->params->get("leftcolumnWidth", "210");
$rightcolumn_width		= $this->params->get("rightcolumnWidth", "210");
$leftinset_width		= $this->params->get("leftinsetWidth", "180");
$rightinset_width		= $this->params->get("rightinsetWidth", "180");
$splitmenu_col			= $this->params->get("splitmenuCol", "rightcol");
$menu_name 				= $this->params->get("menuName", "mainmenu");
$menu_type 				= $this->params->get("menuType", "moomenu");
$menu_rows_per_column   = $this->params->get("menuRowsPerColumn");
$menu_columns           = $this->params->get("menuColumns");
$menu_multicollevel     = $this->params->get("menuMultiColLevel", 1);
$default_font 			= $this->params->get("defaultFont", "default");
$show_date		 		= ($this->params->get("showDate", 1)  == 0)?"false":"true";
$clientside_date		= ($this->params->get("clientSideDate", 1) == 0)?"false":"true";
$show_logo		 		= ($this->params->get("showLogo", 1)  == 0)?"false":"true";
$show_logo_slogan		= ($this->params->get("showLogoslogan", 1)  == 0)?"false":"true";
$logo_slogan 			= $this->params->get("logoSlogan", "Apr 09 Design");
$show_textsizer		 	= ($this->params->get("showTextsizer", 1)  == 0)?"false":"true";
$show_topbutton 		= ($this->params->get("showTopbutton", 1)  == 0)?"false":"true";
$show_copyright 		= ($this->params->get("showCopyright", 1)  == 0)?"false":"true";
$js_compatibility	 	= ($this->params->get("jsCompatibility", 0)  == 0)?"false":"true";

// moomenu options
$moo_bgiframe     		= ($this->params->get("moo_bgiframe'","0") == 0)?"false":"true";
$moo_delay       		= $this->params->get("moo_delay", "500");
$moo_duration    		= $this->params->get("moo_duration", "600");
$moo_fps          		= $this->params->get("moo_fps", "200");
$moo_transition   		= $this->params->get("moo_transition", "Sine.easeOut");

$moo_bg_enabled			= ($this->params->get("moo_bg_enabled","1") == 0)?"false":"true";
$moo_bg_over_duration		= $this->params->get("moo_bg_over_duration", "500");
$moo_bg_over_transition		= $this->params->get("moo_bg_over_transition", "Expo.easeOut");
$moo_bg_out_duration		= $this->params->get("moo_bg_out_duration", "600");
$moo_bg_out_transition		= $this->params->get("moo_bg_out_transition", "Sine.easeOut");

$moo_sub_enabled		= ($this->params->get("moo_sub_enabled","1") == 0)?"false":"true";
$moo_sub_opacity		= $this->params->get("moo_sub_opacity","0.95");
$moo_sub_over_duration		= $this->params->get("moo_sub_over_duration", "50");
$moo_sub_over_transition	= $this->params->get("moo_sub_over_transition", "Expo.easeOut");
$moo_sub_out_duration		= $this->params->get("moo_sub_out_duration", "600");
$moo_sub_out_transition		= $this->params->get("moo_sub_out_transition", "Sine.easeIn");
$moo_sub_offsets_top		= $this->params->get("moo_sub_offsets_top", "0");
$moo_sub_offsets_right		= $this->params->get("moo_sub_offsets_right", "1");
$moo_sub_offsets_bottom		= $this->params->get("moo_sub_offsets_bottom", "0");
$moo_sub_offsets_left		= $this->params->get("moo_sub_offsets_left", "1");
								
require(YOURBASEPATH . "/rt_styleloader.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" >
	<head>
		<jdoc:include type="head" />
		<?php
		require(YOURBASEPATH . DS . "rt_utils.php");
		require(YOURBASEPATH . DS . "rt_head_includes.php");
	?>
	<meta name="google-site-verification" content="65E9U7oAtxKxGOmSQJjBcr1JW7kC_qwxTcXgEj6pQAs" />
	</head>
	<body id="ff-<?php echo $fontfamily; ?>" class="<?php echo $fontstyle; ?> <?php echo $tstyle; ?> <?php echo $bg_style; ?> iehandle">
		<div id="page-bg">
			<div class="wrapper"><div id="body-left" class="png"><div id="body-right" class="png">
				<!--Begin Top Bar-->
				<?php if ($show_date == "true" or $show_textsizer == "true" or $this->countModules('top-left') or $this->countModules('login') or $this->countModules('top-right') or $this->countModules('syndicate')): ?>
				<div id="top-bar">
					<div class="topbar-strip">
						<?php if ($show_date == "true") : ?>
						<div class="date-block">
							<span class="date1"><?php $now = &JFactory::getDate(); echo $now->toFormat('%A'); ?></span>,
							<span class="date2"><?php $now = &JFactory::getDate(); echo $now->toFormat('%B'); ?></span>
							<span class="date3"><?php $now = &JFactory::getDate(); echo $now->toFormat('%d'); ?></span>,
							<span class="date4"><?php $now = &JFactory::getDate(); echo $now->toFormat('%Y'); ?></span>
						</div>
						<?php endif; ?>
						<?php if ($this->countModules('syndicate')) : ?>
						<div class="syndicate-module">
							<jdoc:include type="modules" name="syndicate" style="none" />
						</div>
						<?php endif; ?>
						<?php if ($show_textsizer=="true") : ?>
						<div id="accessibility">
							<div id="buttons">
								<a href="<?php echo JROUTE::_($thisurl . "fontstyle=f-larger"); ?>" title="<?php echo JText::_('INC_FONT_SIZE'); ?>" class="large"><span class="button png">&nbsp;</span></a>
								<a href="<?php echo JROUTE::_($thisurl . "fontstyle=f-smaller"); ?>" title="<?php echo JText::_('DEC_FONT_SIZE'); ?>" class="small"><span class="button png">&nbsp;</span></a>
							</div>
							<div class="textsizer-desc"><?php echo JText::_('TEXT_SIZE'); ?></div>
						</div>
						<?php endif; ?>
						<?php if ($this->countModules('login')) : ?>
							<?php if ($user->guest) : ?>
							<a href="#" id="lock-button" class="login" rel="rokbox[240 210][module=login-module]"><span><?php echo JText::_('LOGIN'); ?></span></a>
							<?php else : ?>
							<a href="#" id="lock-button" rel="rokbox[240 210][module=login-module]"><span><?php echo JText::_('LOGOUT'); ?></span></a>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<?php if ($this->countModules('top-left')) : ?>
					<div class="topbar-left-mod">
						<jdoc:include type="modules" name="top-left" style="xhtml" />
					</div>
					<?php endif; ?>
					<?php if ($this->countModules('top-right')) : ?>
					<div class="topbar-right-mod">
						<jdoc:include type="modules" name="top-right" style="xhtml" />
					</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<!--End Top Bar-->
				<!--Begin Header
				<?php if ($show_logo == "true" or $this->countModules('logo') or $this->countModules('search')) : ?>-->
				<div id="header-bar">
					<!--<?php if ($this->countModules('logo')) : ?>
					<div class="logo-module"><jdoc:include type="modules" name="logo" style="xhtml" /></div>
					<?php elseif ($show_logo == "true") : ?>
					<a href="<?php echo $this->baseurl; ?>" id="logo">
						<?php if ($show_logo_slogan == "true") : ?>
						<span class="logo-text"><?php echo $logo_slogan; ?></span>
						<?php endif; ?>
					</a>
					<?php endif; ?>
					<?php if ($this->countModules('search')) : ?>
					<jdoc:include type="modules" name="search" style="search" />
					<?php endif; ?>-->
				</div>
				<?php endif; ?>
				<?php if($mtype != "none") : ?>
				<div id="horiz-menu" class="<?php echo $mtype; ?>">
				<?php if($mtype != "module") : ?>
					<?php echo $topnav; ?>
				<?php else: ?>
					<jdoc:include type="modules" name="toolbar" style="none" />
				<?php endif; ?>
				<div class="clr"></div>
				</div>
				<?php endif; ?>
				<!--End Header-->
				<!--Begin Showcase Modules-->
				<?php $mClasses = modulesClasses('case5'); if ($this->countModules('showcase') or $this->countModules('showcase2') or $this->countModules('showcase3')) : ?>
				<div class="showcase-surround">
					<div id="showmodules" class="spacer<?php echo $showmod_width; ?>">
						<?php if ($this->countModules('showcase')) : ?>
						<div class="block <?php echo $mClasses['showcase'][0]; ?>">
							<jdoc:include type="modules" name="showcase" style="main" />
						</div>
						<?php endif; ?>
						<?php if ($this->countModules('showcase2')) : ?>
						<div class="block <?php echo $mClasses['showcase2'][0]; ?>">
							<jdoc:include type="modules" name="showcase2" style="main" />
						</div>
						<?php endif; ?>
						<?php if ($this->countModules('showcase3')) : ?>
						<div class="block <?php echo $mClasses['showcase3'][0]; ?>">
							<jdoc:include type="modules" name="showcase3" style="main" />
						</div>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>
				<!--End Showcase Modules-->
				<div id="main-body">
					<div id="main-body-surround">
						<!--Begin Main Content Block-->
						<div id="main-content" class="<?php echo $col_mode; ?>">
						    <div class="colmask leftmenu">
						        <div class="colmid">
    					    	    <div class="colright">
        						        <!--Begin col1wrap -->    
            						    <div class="col1wrap">
            						        <div class="col1pad">
            						            <div class="col1">
                    						        <div id="maincol2">
                    									<div class="maincol2-padding">
															<?php if ($this->countModules('feature')) : ?>
	                    									<div id="feature-section">
	                    										<jdoc:include type="modules" name="feature" style="feature" />
	                    									</div>
	                    									<?php endif; ?>
															<?php if ($this->countModules('newsflash')) : ?>
	                    									<div id="newsflash-bar">
	                    										<jdoc:include type="modules" name="newsflash" style="newsflash" />
	                    									</div>
	                    									<?php endif; ?>
                    									<?php $mClasses = modulesClasses('case1'); if ($this->countModules('user1') or $this->countModules('user2') or $this->countModules('user3')) : ?>
                    									<div id="mainmodules" class="spacer<?php echo $mainmod_width; ?>">
                    										<?php if ($this->countModules('user1')) : ?>
                    										<div class="block <?php echo $mClasses['user1'][0]; ?>">
                    											<jdoc:include type="modules" name="user1" style="main" />
                    										</div>
                    										<?php endif; ?>
                    										<?php if ($this->countModules('user2')) : ?>
                    										<div class="block <?php echo $mClasses['user2'][0]; ?>">
                    											<jdoc:include type="modules" name="user2" style="main" />
                    										</div>
                    										<?php endif; ?>
                    										<?php if ($this->countModules('user3')) : ?>
                    										<div class="block <?php echo $mClasses['user3'][0]; ?>">
                    											<jdoc:include type="modules" name="user3" style="main" />
                    										</div>
                    										<?php endif; ?>
                    									</div>
                    									<?php endif; ?>
                    									<?php if ($this->countModules('breadcrumb')) : ?>
                    									<div id="breadcrumbs">
															<a href="<?php echo $this->baseurl; ?>" id="breadcrumbs-home"></a>
                    										<jdoc:include type="modules" name="breadcrumb" style="none" />
                    									</div>
                    									<?php endif; ?>
                    									<div class="bodycontent">
                    										<?php if ($this->countModules('inset2') and !$editmode) : ?>
                    										<div id="inset-block-right"><div class="right-padding">
                    											<jdoc:include type="modules" name="inset2" style="sidebar" />
                    										</div></div>
                    										<?php endif; ?>
                    										<?php if ($this->countModules('inset') and !$editmode) : ?>
                    										<div id="inset-block-left"><div class="left-padding">
                    											<jdoc:include type="modules" name="inset" style="sidebar" />
                    										</div></div>
                    										<?php endif; ?>
                    										<div id="maincontent-block">
                												<jdoc:include type="message" />
                												<?php if (!($frontpage_component == 'hide' and JRequest::getVar('view') == 'frontpage')): ?>
                												<jdoc:include type="component" />
                												<?php endif; ?>
                    										</div>
                    										</div>
                    										<div class="clr"></div>
															<?php if ($this->countModules('rokmicronews')) : ?>
															<div id="rokmicronews">
																<jdoc:include type="modules" name="rokmicronews" style="rokmicronews" />
															</div>
															<?php endif; ?>
                        									<?php $mClasses = modulesClasses('case2'); if ($this->countModules('user4') or $this->countModules('user5') or $this->countModules('user6')) : ?>
                        									<div id="mainmodules2" class="spacer<?php echo $mainmod2_width; ?>">
                        										<?php if ($this->countModules('user4')) : ?>
                        										<div class="block <?php echo $mClasses['user4'][0]; ?>">
                        											<jdoc:include type="modules" name="user4" style="main" />
                        										</div>
                        										<?php endif; ?>
                        										<?php if ($this->countModules('user5')) : ?>
                        										<div class="block <?php echo $mClasses['user5'][0]; ?>">
                        											<jdoc:include type="modules" name="user5" style="main" />
                        										</div>
                        										<?php endif; ?>
                        										<?php if ($this->countModules('user6')) : ?>
                        										<div class="block <?php echo $mClasses['user6'][0]; ?>">
                        											<jdoc:include type="modules" name="user6" style="main" />
                        										</div>
                        										<?php endif; ?>
                        									</div>
                        									<?php endif; ?>
                    									</div>
                    								</div>    
                    							</div>
            						        </div>
            						    </div>
            						    <!--End col1wrap -->
           						        <!--Begin col2 -->
           						        <?php if ($leftcolumn_width != 0) : ?>
            						    <div class="col2">
                							<div id="leftcol">
                                                <div id="leftcol-bg">
                									<?php if ($subnav and $splitmenu_col=="leftcol") : ?>
                									<div class="sidenav-block">
                										<?php echo $subnav; ?>
                									</div>
                									<?php endif; ?>
                									<jdoc:include type="modules" name="left" style="sidebar" />
                									<?php if (!isset($active)) :?>
        											<jdoc:include type="modules" name="inactive" style="sidebar" />    
        											<?php endif; ?>
                                                </div>
                							</div>
            						    </div>
            						    <?php endif; ?> 
            						    <!---End col2 -->
            						    <!--Begin col3 -->
            						    <?php if ($rightcolumn_width != 0) : ?>
            						    <div class="col3">
                							<div id="rightcol">
           										<?php if ($subnav and $splitmenu_col=="rightcol") : ?>
            									<div class="sidenav-block">
            										<?php echo $subnav; ?>
            									</div>
            									<?php endif; ?>
            									<jdoc:include type="modules" name="right" style="sidebar" />
                							</div>
            						    </div>
            						    <?php endif; ?> 
            						    <!--End col3-->
        							</div>
    							</div>
							</div>
						</div>
						<!--End Main Content Block-->
					</div>
					<!--Begin Bottom Main Modules-->
					<?php $mClasses = modulesClasses('case3'); if ($this->countModules('user7') or $this->countModules('user8') or $this->countModules('user9')) : ?>
					<div id="bottom-main">
						<div id="mainmodules3" class="spacer<?php echo $mainmod3_width; ?>">
							<?php if ($this->countModules('user7')) : ?>
							<div class="block <?php echo $mClasses['user7'][0]; ?>">
								<jdoc:include type="modules" name="user7" style="main" />
							</div>
							<?php endif; ?>
							<?php if ($this->countModules('user8')) : ?>
							<div class="block <?php echo $mClasses['user8'][0]; ?>">
								<jdoc:include type="modules" name="user8" style="main" />
							</div>
							<?php endif; ?>
							<?php if ($this->countModules('user9')) : ?>
							<div class="block <?php echo $mClasses['user9'][0]; ?>">
								<jdoc:include type="modules" name="user9" style="main" />
							</div>
							<?php endif; ?>
						</div>
					</div>
					<?php endif; ?>
					<!--End Bottom Main Modules-->
					<!--Begin Bottom Bar-->
					<?php if ($show_topbutton == "true" or ($this->countModules('bottom-menu'))) : ?>
					<div id="botbar">
						<?php if ($this->countModules('bottom-menu')) : ?>
						<div id="bottom-menu">
							<jdoc:include type="modules" name="bottom-menu" style="xhtml" />
						</div>
						<?php endif; ?>
						<?php if ($show_topbutton == "true") : ?>
						<?php if ($show_topbutton == "true" and ($this->countModules('bottom-menu')=="0")) : ?>
						<div class="top-button-spacer"></div>
						<?php endif; ?>
						<div id="top-button"><a href="#" id="top-scroll" class="top-button-desc"><?php echo JText::_('TOP'); ?></a></div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<!--End Bottom Bar-->
					<!--Begin Bottom Section-->
					<?php if ($show_copyright == "true" or $this->countModules('footer') or ($this->countModules('bottom') or $this->countModules('bottom2') or $this->countModules('bottom3'))) : ?>
					<div id="bottom">
						<?php $mClasses = modulesClasses('case4'); if ($this->countModules('bottom') or $this->countModules('bottom2') or $this->countModules('bottom3')) : ?>
						<div id="mainmodules4" class="spacer<?php echo $mainmod4_width; ?>">
							<?php if ($this->countModules('bottom')) : ?>
							<div class="block <?php echo $mClasses['bottom'][0]; ?>">
								<jdoc:include type="modules" name="bottom" style="bottom" />
							</div>
							<?php endif; ?>
							<?php if ($this->countModules('bottom2')) : ?>
							<div class="block <?php echo $mClasses['bottom2'][0]; ?>">
								<jdoc:include type="modules" name="bottom2" style="bottom" />
							</div>
							<?php endif; ?>
							<?php if ($this->countModules('bottom3')) : ?>
							<div class="block <?php echo $mClasses['bottom3'][0]; ?>">
								<jdoc:include type="modules" name="bottom3" style="bottom" />
							</div>
							<?php endif; ?>
						</div>
						<?php endif; ?>
						<?php if ($show_copyright == "true") : ?>
						<div class="copyright-block">
							<div id="copyright">
								&copy; <?php echo JText::_('COPYRIGHT'); ?>
							</div>
							<a href="http://www.rockettheme.com/" title="<?php echo JText::_('ROCKETTHEME_JTC'); ?>" id="rocket"></a>
						</div>
						<?php else: ?>
						<div class="footer-mod">
							<jdoc:include type="modules" name="footer" style="xhtml" />
						</div>
						<?php endif; ?>
					</div>
					<?php endif; ?>
					<!--End Bottom Section-->
				</div>
			</div></div></div>
		</div>
		<div class="footer-bottom"></div>
		<?php if ($this->countModules('debug')) : ?>
		<div id="debug-mod">
			<jdoc:include type="modules" name="debug" style="none" />
		</div>
		<?php endif; ?>
		<?php if ($this->countModules('login')) : ?>
		<div id="login-module">
			<?php if ($user->guest) : ?>
			<jdoc:include type="modules" name="login" style="xhtml" />
			<?php else : ?>
			<div class="logout">
				<jdoc:include type="modules" name="login" style="xhtml" />
			</div>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</body>
</html>
