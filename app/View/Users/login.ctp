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
			<?php echo $this -> Form -> create('User', array('action' => 'login', 'class' => 'form-horizontal')); ?>
	
				<div class="control-group">
					<label class="control-label" for="UserUsername"><?php echo __('Username'); ?></label>
					<div class="controls">
						<input id="UserUsername" type="text" value="" maxlength="50" name="data[User][username]">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputPassword">Password</label>
					<div class="controls">
						<?php echo $this -> Form -> input('password', array('label' => __('Password'), 'div' => false, 'label' => false)); ?>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<label class="checkbox">
							<?php echo $this -> Form -> input('auto_login', array('type' => 'checkbox', 'label' => false, 'div' => false)); ?>
							Remember me </label>
						<button type="submit" class="btn">
							Sign in
						</button>
					</div>
				</div>
			<?php echo $this -> Form -> end(); ?>
		</div>
	</div>
</div>