<div class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Reset Password');?></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				<?php echo __('Please enter in a new password below to reset your password.');?>
			</div>
		</div>
		<div class="component-view">
			<?php echo $this -> Form -> create('User', array('url' => '/users/resetPassword/'.$this->request->params['pass'][0].'/'.$this->request->params['pass'][1],  ));?>
			<fieldset>
				<ul class="form-fields unstyled">
					<li>
						<div class="label-wrapper">
							<label for="UserNewPassword"><?php echo __('Password')
								?></label>
						</div>
						<?php echo $this -> Form -> input('new_password', array('div' => false, 'label' => false, 'type' => 'password'));?>
					</li>
					<li>
						<div class="label-wrapper">
							<label for="UserConfirmPassword"><?php echo __('Confirm Password')
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
