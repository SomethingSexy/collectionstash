<div class="component" id="forgot-password-component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Forgot Password');?></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				<p>
					<?php echo __('If you have forgotten you password, please enter your e-mail address below that is used for your account.  We will send you a link to reset your password.');?>
				</p>
			</div>
		</div>
		<div class="component-view">
			<?php echo $this -> Form -> create('User', array('url' => array('controller' => 'forgotten_requests', 'action' => 'forgotPassword')));?>
			<fieldset>
				<ul class="form-fields">
					<li>
						<div class="label-wrapper">
							<label for="UserEmail"><?php __('Email')
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