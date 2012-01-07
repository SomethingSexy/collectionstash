<div class="component">
  <div class="inside">
    <div class="component-title">
      <h2><?php echo __('Registration');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div><?php echo __('Please fill out the form below to register an account.');?></div> 
    </div>
    <div class="component-view">
      <?php echo $this->Form->create('User' , array('action' => 'register'));?>
        <fieldset>
          <ul class="form-fields">
            <li>
              <div class="label-wrapper">
                <label for="UserUsername"><?php echo __('User Name') ?></label>
              </div>
              <?php echo $this->Form->input('username', array('div' => false, 'label' => false, 'after' => $this->Form->error('username_unique', 'The username is taken. Please try again.')));  ?>
            </li>
            <li>
              <div class="label-wrapper">
                <label for="UserNewPassword"><?php echo __('Password') ?></label>
              </div>              
              <?php echo $this->Form->input('new_password', array('div' => false, 'label' => false, 'type' => 'password'));?>
            </li> 
            <li>
              <div class="label-wrapper">
                <label for="UserConfirmPassword"><?php echo __('Confirm Password') ?></label>
              </div>
              <?php echo $this->Form->input('confirm_password', array('div' => false, 'label' => false, 'type' => 'password'));?>
            </li>
            <li>
              <div class="label-wrapper">
                <label for="UserFirstName"><?php echo __('First Name') ?></label>
              </div>      
              <?php echo $this->Form->input('first_name', array('div' => false, 'label' => false));?>
            </li>
            <li>
              <div class="label-wrapper">
                <label for="UserLastName"><?php echo __('Last Name') ?></label>
              </div>      
              <?php echo $this->Form->input('last_name', array('div' => false, 'label' => false));?>
            </li> 
            <li>
              <div class="label-wrapper">
                <label for="UserEmail"><?php echo __('Email') ?></label>
              </div> 
              <?php echo $this->Form->input('email', array('div' => false, 'label' => false));?>
            </li>
          </ul>
        </fieldset>
      <?php echo $this->Form->end(__('Submit', true));?>
          </div>    
  </div>
</div>
