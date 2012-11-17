<div class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Log In'); ?></h2>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="component-info">
			<div>
				<p>
					<?php echo __('Welcome to Collection Stash, please log in.'); ?>
				</p>
				<p>
					<?php echo $this -> Html -> link('Forgot Password?', array('admin' => false, 'action' => 'forgotPassword', 'controller' => 'forgotten_requests')); ?>
				</p>
			</div>
		</div>
		<div class="component-view">
			<?php echo $this -> Form -> create('User', array('action' => 'login')); ?>
			<fieldset>
				<ul class="form-fields unstyled">
					<li>
						<div class="input text">
							<div class="label-wrapper">
								<label for="UserUsername"><?php echo __('Username');?></label>
							</div>
							<input id="UserUsername" type="text" value="" maxlength="50" name="data[User][username]">
						</div>
					</li>
					<li>
						<?php echo $this -> Form -> input('password', array('label' => __('Password'), 'before' => '<div class="label-wrapper">', 'between' => '</div>')); ?>
					</li>
					<li>
						<div class="input checkbox">
							<div class="label-wrapper">
								<label for="CollectibleExclusive"><?php echo __('Remember Me'); ?></label>
							</div>
							<?php echo $this -> Form -> input('auto_login', array('type' => 'checkbox', 'label' => false, 'div' => false)); ?>
						</div>
					</li>
				</ul>
			</fieldset>
			<input type="submit" value="Login" class="btn btn-primary"/>
			<?php echo $this -> Form -> end(); ?>
		</div>
	</div>
</div>