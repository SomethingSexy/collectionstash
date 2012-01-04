<div class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php __('Reset Password');?></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				<?php __('Please enter in a new password below to reset your password.');?>
			</div>
		</div>
		<div class="component-view">
			<?php echo $this -> Form -> create('User', array('url' => '/users/resetPassword/'.$this->params['pass'][0],  ));?>
			<fieldset>
				<ul class="form-fields">
					<li>
						<div class="label-wrapper">
							<label for="UserNewPassword"><?php __('Password')
								?></label>
						</div>
						<?php echo $this -> Form -> input('new_password', array('div' => false, 'label' => false, 'type' => 'password'));?>
					</li>
					<li>
						<div class="label-wrapper">
							<label for="UserConfirmPassword"><?php __('Confirm Password')
								?></label>
						</div>
						<?php echo $this -> Form -> input('confirm_password', array('div' => false, 'label' => false, 'type' => 'password'));?>
					</li>
				</ul>
			</fieldset>
			<?php echo $this -> Form -> end(__('Change', true));?>
		</div>
	</div>
</div>
