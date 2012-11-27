<?php
/** $Id: default_address.php 11328 2008-12-12 19:22:41Z kdevine $ */
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<div class="clr"></div>

<?php if ( ( $this->contact->params->get( 'address_check' ) > 0 ) &&  ( $this->contact->address || $this->contact->suburb  || $this->contact->state || $this->contact->country || $this->contact->postcode ) ) : ?>
<div class="main-address">
	<?php if ( $this->contact->address && $this->contact->params->get( 'show_street_address' ) ) : ?>
	<div class="surround"><div class="street-address">
		<div class="icon"><?php if ( $this->contact->params->get( 'address_check' ) > 0 ) : ?><?php echo $this->contact->params->get( 'marker_address' ); ?><?php endif; ?></div>
		<div class="data">
			<?php echo nl2br($this->contact->address); ?>
		</div>
	</div></div>
	<?php endif; ?>
	<?php if ( $this->contact->suburb && $this->contact->params->get( 'show_suburb' ) ) : ?>
	<div class="surround"><div class="suburb">
		<div class="icon">&nbsp;</div>	
		<div class="data">
			<?php echo $this->contact->suburb; ?>
		</div>
	</div></div>
	<?php endif; ?>
	<?php if ( $this->contact->state && $this->contact->params->get( 'show_state' ) ) : ?>
	<div class="surround"><div class="state">
		<div class="icon">&nbsp;</div>	
		<div class="data">
			<?php echo $this->contact->state; ?>
		</div>
	</div></div>
	<?php endif; ?>
	<?php if ( $this->contact->postcode && $this->contact->params->get( 'show_postcode' ) ) : ?>
	<div class="surround"><div class="postcode">
		<div class="icon">&nbsp;</div>	
		<div class="data">
			<?php echo $this->contact->postcode; ?>
		</div>
	</div></div>
	<?php endif; ?>
	<?php if ( $this->contact->country && $this->contact->params->get( 'show_country' ) ) : ?>
	<div class="surround"><div class="country">
		<div class="icon">&nbsp;</div>
		<div class="data">
			<?php echo $this->contact->country; ?>
		</div>
	</div></div>
	<?php endif; ?>
</div>
<br />
<?php endif; ?>

<?php if ( ($this->contact->email_to && $this->contact->params->get( 'show_email' )) || 
			($this->contact->telephone && $this->contact->params->get( 'show_telephone' )) || 
			($this->contact->fax && $this->contact->params->get( 'show_fax' )) || 
			($this->contact->mobile && $this->contact->params->get( 'show_mobile' )) || 
			($this->contact->webpage && $this->contact->params->get( 'show_webpage' )) ) : ?>
	<div class="other">
		<?php if ( $this->contact->email_to && $this->contact->params->get( 'show_email' ) ) : ?>
		<div class="surround"><div class="email">
			<div class="icon">
				<?php echo $this->contact->params->get( 'marker_email' ); ?>
			</div>
			<div class="data">
				<?php echo $this->contact->email_to; ?>
			</div>
		</div></div>
		<?php endif; ?>


		<?php if ( $this->contact->telephone && $this->contact->params->get( 'show_telephone' ) ) : ?>
		<div class="surround"><div class="telephone">
			<div class="icon">
				<?php echo $this->contact->params->get( 'marker_telephone' ); ?>
			</div>
			<div class="data">
				<?php echo nl2br($this->contact->telephone); ?>
			</div>
		</div></div>
		<?php endif; ?>

		<?php if ( $this->contact->fax && $this->contact->params->get( 'show_fax' ) ) : ?>
		<div class="surround"><div class="fax">
			<div class="icon">
				<?php echo $this->contact->params->get( 'marker_fax' ); ?>
			</div>
			<div class="data">
				<?php echo nl2br($this->contact->fax); ?>
			</div>
		</div></div>
		<?php endif; ?>

		<?php if ( $this->contact->mobile && $this->contact->params->get( 'show_mobile' ) ) :?>
		<div class="surround"><div class="mobile">
			<div class="icon">
			<?php echo $this->contact->params->get( 'marker_mobile' ); ?>
			</div>
			<div class="data">
				<?php echo nl2br($this->contact->mobile); ?>
			</div>
		</div></div>
		<?php endif; ?>

		<?php if ( $this->contact->webpage && $this->contact->params->get( 'show_webpage' )) : ?>
		<div class="surround"><div class="webpage">
			<div class="icon">&nbsp;</div>
			<div class="data">
				<a href="<?php echo $this->contact->webpage; ?>" target="_blank"><?php echo $this->contact->webpage; ?></a>
			</div>
		</div></div>
		<?php endif; ?>
	</div>
	
<?php endif; ?>

<br />

<?php if ( $this->contact->misc && $this->contact->params->get( 'show_misc' ) ) : ?>
<div class="surround"><div class="misc">
	<div class="icon">
		<?php echo $this->contact->params->get( 'marker_misc' ); ?>
	</div>
	<div class="data">
		<?php echo $this->contact->misc; ?>
	</div>
</div></div>
<?php endif; ?>