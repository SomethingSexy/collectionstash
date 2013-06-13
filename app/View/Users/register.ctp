<div class="page-header">
    <h1><?php echo __('Registration'); ?></h1>
</div>
<?php echo $this -> element('flash'); ?>

    
<p><?php echo __('Please fill out the form below to register an account.'); ?></p> 
<?php echo $this -> Form -> create('User', array('class' => 'form-horizontal', 'action' => 'register')); ?>
<div class="control-group <?php if ($this -> Form -> isFieldError('username')) { echo 'error'; } ?>">
	<label class="control-label" for="UserUsername"><?php echo __('User Name'); ?></label>
	<div class="controls">
		<?php echo $this -> Form -> input('username', array('div' => false, 'label' => false)); ?>
	</div>
</div>
<div class="control-group <?php if ($this -> Form -> isFieldError('new_password')) { echo 'error'; } ?>">
	<label class="control-label" for="UserNewPassword"><?php echo __('Password'); ?></label>
	<div class="controls">
		<?php echo $this -> Form -> password('new_password', array('div' => false, 'label' => false));
		if ($this -> Form -> isFieldError('new_password')) {
			echo '<span class="help-inline">' . $this -> Form -> error('new_password') . '</span>';
		}
		?>
	</div>
</div>

<div class="control-group <?php if ($this -> Form -> isFieldError('confirm_password')) { echo 'error'; } ?>">
	<label class="control-label" for="UserConfirmPassword"><?php echo __('Confirm Password'); ?></label>
	<div class="controls">
		<?php echo $this -> Form -> password('confirm_password', array('div' => false, 'label' => false)); ?>
	</div>
</div>

<div class="control-group <?php if ($this -> Form -> isFieldError('first_name')) { echo 'error'; } ?>">
	<label class="control-label" for="UserFirstName"><?php echo __('First Name'); ?></label>
	<div class="controls">
		<?php echo $this -> Form -> input('first_name', array('div' => false, 'label' => false)); ?>
	</div>
</div>

<div class="control-group <?php if ($this -> Form -> isFieldError('last_name')) { echo 'error'; } ?>">
	<label class="control-label" for="UserLastName"><?php echo __('Last Name'); ?></label>
	<div class="controls">
		<?php echo $this -> Form -> input('last_name', array('div' => false, 'label' => false)); ?>
	</div>
</div>

<div class="control-group <?php if ($this -> Form -> isFieldError('email')) { echo 'error'; } ?>">
	<label class="control-label" for="UserEmail"><?php echo __('Email'); ?></label>
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
 
