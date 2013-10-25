<div class="widget">
	<div class="widget-header">
	    <h3><?php echo __('Forgot Password'); ?></h3>
	</div>
	<div class="widget-content">
		<p>
			<?php echo __('If you have forgotten you password, please enter your e-mail address below that is used for your account.  We will send you a link to reset your password.'); ?>
		</p>
		<?php echo $this -> element('flash'); ?>
		<?php echo $this -> Form -> create('User', array('class' => 'form-horizontal', 'url' => array('controller' => 'forgotten_requests', 'action' => 'forgotPassword'))); ?>
		<div class="form-group">
			<label class="col-lg-3 control-label" for="UserEmail"><?php echo __('Email')?></label>
			<div class="col-lg-6">
				<?php echo $this -> Form -> input('email', array('class' => 'form-control', 'div' => false, 'label' => false)); ?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-offset-3 col-lg-9">
				<button type="submit" class="btn btn-primary">
					Submit
				</button>
			</div>
		</div>
		<?php echo $this -> Form -> end(); ?>
	</div>
</div>


