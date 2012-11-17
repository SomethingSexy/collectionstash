<div class="component" id="forgot-password-component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Reset Password');?></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				<p>
					<?php echo __('To better enhance security we are asking you to please reset your password.  This is a one time reset but it will help secure your data.  Please enter your email address below and follow the instructions to reset your password.');?>
				</p>
			</div>
		</div>
		<div class="component-view">
			<?php echo $this -> Form -> create('User', array('url' => array('controller' => 'forgotten_requests', 'action' => 'forceResetPassword')));?>
			<fieldset>
				<ul class="form-fields unstyled">
					<li>
						<div class="label-wrapper">
							<label for="UserEmail"><?php echo __('Email')
								?></label>
						</div>
						<?php echo $this -> Form -> input('email', array('div' => false, 'label' => false));?>
					</li>
				</ul>
			</fieldset>
			<?php echo $this -> Form -> end(__('Submit', true));?>
		</div>
	</div>
</div>