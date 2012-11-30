<div class="component">
  <div class="inside">
    <div class="component-title">
      <h2><?php echo __('Registration'); ?></h2>
    </div>
    <?php echo $this -> element('flash'); ?>
    <div class="component-info">
      <div><?php echo __('Please fill out the form below to register an account.'); ?></div> 
    </div>
    <div class="component-view">
      <?php echo $this -> Form -> create('User', array('action' => 'register')); ?>
        <fieldset>
          <ul class="form-fields unstyled">
            <li>
				<?php echo $this -> Form -> input('username', array('label' => __('User Name'), 'before' => '<div class="label-wrapper">', 'between' => '</div>')); ?>
			</li>  
            <li>
				<?php echo $this -> Form -> input('new_password', array('label' => __('Password'), 'before' => '<div class="label-wrapper">', 'between' => '</div>')); ?>
			</li>            
            <li>
				<?php echo $this -> Form -> input('confirm_password', array('label' => __('Confirm Password'), 'before' => '<div class="label-wrapper">', 'between' => '</div>')); ?>
			</li>
            <li>
				<?php echo $this -> Form -> input('first_name', array('label' => __('First Name'), 'before' => '<div class="label-wrapper">', 'between' => '</div>')); ?>
			</li>
            <li>
				<?php echo $this -> Form -> input('last_name', array('label' => __('Last Name'), 'before' => '<div class="label-wrapper">', 'between' => '</div>')); ?>
			</li>
			<li>
				<?php echo $this -> Form -> input('email', array('label' => __('Email'), 'before' => '<div class="label-wrapper">', 'between' => '</div>')); ?>
			</li>	
          </ul>
        </fieldset>
        <input type="submit" value="Submit" class="btn btn-primary">
      <?php echo $this -> Form -> end(); ?>
          </div>    
  </div>
</div>
