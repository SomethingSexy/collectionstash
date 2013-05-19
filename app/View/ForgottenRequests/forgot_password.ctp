<div class="page-header">
    <h1><?php echo __('Forgot Password'); ?></h1>
</div>

<p>
	<?php echo __('If you have forgotten you password, please enter your e-mail address below that is used for your account.  We will send you a link to reset your password.'); ?>
</p>
<?php echo $this -> element('flash'); ?>
<?php echo $this -> Form -> create('User', array( 'class' => 'form-horizontal', 'url' => array('controller' => 'forgotten_requests', 'action' => 'forgotPassword'))); ?>
<div class="control-group">
	<label class="control-label" for="UserEmail"><?php echo __('Email')?></label>
	<div class="controls">
		<?php echo $this -> Form -> input('email', array('div' => false, 'label' => false)); ?>
	</div>
</div>
<div class="control-group">
	<div class="controls">
		<button type="submit" class="btn">
			Submit
		</button>
	</div>
</div>
<?php echo $this -> Form -> end(); ?>
	