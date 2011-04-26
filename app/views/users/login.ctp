<?php echo $this->Html->css('login/login',null,array('inline'=>false)); ?>
<div class="component">
  <div class="inside">
  	<div class="component-title">
  		<h2><?php __('Log In');?></h2>
  	</div>
  	<?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>Welcome to Collection Stash, please log in.</div> 
    </div>
    <div class="component-view">
      <?php echo $form->create('User', array('action' => 'login')); ?>
        <fieldset>
          <ul class="form-fields">
            <li>
              <div class="label-wrapper">
                <label for=""><?php __('Username') ?></label>
              </div>
            <?php echo $form->input('username', array('div' => false,'label'=> false));?>
           </li>
           <li>
              <div class="label-wrapper">
                <label for=""><?php __('Password') ?></label>
              </div>           
            <?php echo $form->input('password', array('div' => false, 'label'=> false));?>
           </li>
          </ul>
        </fieldset>
      <?php echo $form->end('Login');?>
    </div>    
  </div>
</div>