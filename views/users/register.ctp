<div class="container2">
  <div class="info">
  <h2>Registration</h2>
  <div>Please fill out the form below to register an account..</div>
  </div>
<?php echo $this->Form->create('User' , array('action' => 'register'));?>
  <fieldset>
    <ul class="form-fields">
      <li><?php echo $this->Form->input('username', array('div' => false, 'label' => 'Username', 'after' => $form->error('username_unique', 'The username is taken. Please try again.')));?></li>
      <li><?php echo $this->Form->input('new_password', array('div' => false, 'label' => 'Password', 'type' => 'password'));?></li>
      <li><?php echo $this->Form->input('confirm_password', array('div' => false, 'label' => 'Confirm Password', 'type' => 'password'));?></li>
      <li><?php echo $this->Form->input('first_name', array('div' => false, 'label' => 'First Name'));?></li>
      <li><?php echo $this->Form->input('last_name', array('div' => false, 'label' => 'Last Name'));?></li>
      <li><?php echo $this->Form->input('email', array('div' => false, 'label' => 'Email'));?></li>
 </ul>
  </fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
