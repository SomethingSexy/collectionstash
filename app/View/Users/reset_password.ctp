<div class="widget">
	<div class="widget-header">
		<h3><?php echo __('Reset Password'); ?></h3>
	</div>
	<div class="widget-content">
		<?php echo $this -> element('flash'); ?>
		<p><?php echo __('Please enter in a new password below to reset your password.'); ?></p>
		<?php echo $this -> Form -> create('User', array('url' => '/users/resetPassword/' . $this -> request -> params['pass'][0] . '/' . $this -> request -> params['pass'][1], 'class' => 'form-horizontal')); ?>
			<fieldset>
				<div class="control-group">
					<label class="control-label" for="UserNewPassword"><?php echo __('Password'); ?></label>
					<div class="controls">
						<?php echo $this -> Form -> input('new_password', array('div' => false, 'label' => false, 'type' => 'password')); ?>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="UserConfirmPassword"><?php echo __('Confirm Password'); ?></label>
					<div class="controls">
						<?php echo $this -> Form -> input('confirm_password', array('div' => false, 'label' => false, 'type' => 'password')); ?>
					</div>
				</div>
				<div class="form-actions">
					<button class="btn btn-primary" type="submit"><?php echo __('Change');?></button>
				</div>
			</fieldset>
		<?php echo $this -> Form -> end(); ?>
	</div>
</div>
