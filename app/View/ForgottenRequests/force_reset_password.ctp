<div class="widget" id="forgot-password-component">
	<div class="widget-header">
		<h3><?php echo __('Reset Password'); ?></h3>
	</div>
		
	<div class="widget-content">
		<?php echo $this -> element('flash'); ?>
		<p><?php echo __('To better enhance security we are asking you to please reset your password.  This is a one time reset but it will help secure your data.  Please enter your email address below and follow the instructions to reset your password.'); ?></p>
		<?php echo $this -> Form -> create('User', array('url' => array('controller' => 'forgotten_requests', 'action' => 'forceResetPassword'))); ?>
		<fieldset>
			<ul class="form-fields unstyled">
				<li>
					<div class="label-wrapper">
						<label for="UserEmail"><?php echo __('Email')
							?></label>
					</div>
					<?php echo $this -> Form -> input('email', array('div' => false, 'label' => false)); ?>
				</li>
			</ul>
		</fieldset>
		<?php echo $this -> Form -> end(__('Submit', true)); ?>
	</div>
</div>