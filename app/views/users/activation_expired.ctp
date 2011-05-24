<div class="component" id="registration-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php __('Registration');?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-view">
			<p><?php __('Your activation code has expired or is invalid.  If you wish to receive a new activation code, please click the link below.'); ?></p>
			<p><a class="link" href="/users/resendActivation/<?php echo $userId; ?>">Resend activation code</a></p>
		</div>
	</div>
</div>
