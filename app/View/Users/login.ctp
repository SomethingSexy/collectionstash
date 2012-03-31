<?php echo $this->Html->css('login/login',null,array('inline'=>false)); ?>
<div class="component">
  <div class="inside">
  	<div class="component-title">
  		<h2><?php echo __('Log In');?></h2>
  	</div>
  	<?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>
      	<p><?php echo __('Welcome to Collection Stash, please log in.');?></p>
      	<p><?php echo $this -> Html -> link('Forgot Password?', array('admin'=> false, 'action' => 'forgotPassword', 'controller' => 'forgotten_requests'));?></p>
      </div> 
    </div>
    <div class="component-view">
      <?php echo $this -> Form ->create('User', array('action' => 'login')); ?>
        <fieldset>
          <ul class="form-fields">
            <li>
                <?php echo $this -> Form -> input('username', array('label' => __('Username'), 'before' => '<div class="label-wrapper">', 'between' => '</div>'));?>
            </li>
            <li>
                <?php echo $this -> Form -> input('password', array('label' => __('Password'), 'before' => '<div class="label-wrapper">', 'between' => '</div>'));?>
            </li>
          </ul>
        </fieldset>
      <?php echo $this -> Form -> end('Login');?>
    </div>    
  </div>
</div>