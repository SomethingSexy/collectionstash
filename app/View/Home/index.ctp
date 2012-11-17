<?php echo $this -> Minify -> script('js/home', array('inline' => false)); ?>
<?php echo $this -> Minify -> css('css/layout/home'); ?>
<div id="home-components">
	<div class="site-information">
		<h3>
			<span>Are you squirreling?</span>
		</h3>
		
		<ul class="information">
			<li>
				<span class="icon"><img src="/img/icon/hammer-gray-big.png"/></span>
				<h4>Build</h4>
				<span class="text">Help build the largest collectible database and community by submitting new collectibles you own or enjoy.</span>
			</li>
			<li >
				<span class="icon"><img src="/img/icon/add_stash_link.png"/></span>
				<h4>Stash</h4>
				<span class="text">Add collectibles from our growing database to your stash to build your collection.</span>
			</li>	
			<li class="third">
				<span class="icon"><img src="/img/icon/chat-gray.png"/></span>
				<h4>Discuss</h4>
				<span class="text">Discuss your stash and collectibles with like-minded members with our custom chat feature.</span>
			</li>		
			<li>
				<span class="icon"><img src="/img/icon/group-gray-big.png"/></span>
				<h4>Share</h4>
				<span class="text">Share your stash with the community and friends.</span>
			</li>	
		</ul>	
		
		
		<h4>
			<span>How it works:</span>
		</h4>	
		
		<ol class="how-it-works">
			<li>
				<?php echo __('Search for a collectible you own.'); ?>
			</li>
			<li>
				<?php echo __('Click the acorn icon on any collectible to add it to your stash.'); ?>
			</li>
			<li>
				<?php echo __('If you don\'t see a collectible in our database click the "Submit New Collectible" link to submit it to our catalog.'); ?>
			</li>
			<li>
				<?php echo __('Share and discuss your stash with the community!'); ?>
			</li>
		</ol>	
				
		<!--<ul class="buttons">
			<li>
				<a href="/collectibles/catalog"><?php echo __('Discover'); ?></a>
				
			</li>
			<li>
				<a href="/users/register"><?php echo __('Register'); ?></a>
			</li>
		</ul> -->
	</div>
</div>


<div class="component login-registration">
  <div class="inside">
    <div class="component-title">
      <h2><span><?php echo __('New here?'); ?></span><span class="orange"><?php echo __(' Give it a try.'); ?></span><span><?php echo __(' Already a member?'); ?></span><span class="orange"><?php echo __(' Sign in.'); ?></span></h2>
    </div>
    <?php echo $this -> element('flash'); ?>
    <div class="component-view">
    	<div class="login">
    	<?php echo $this -> Form -> create('User', array('action' => 'login')); ?>
		<fieldset>
			<legend><?php echo __('Sign In');?></legend>
			<ul class="form-fields unstyled">
				<li>
					<div class="input text">
						<div class="label-wrapper">
							<label for="UserUsername"><?php echo __('Username'); ?></label>
						</div>
						<input id="UserUsername" type="text" value="" maxlength="50" name="data[User][username]">
					</div>
				</li>
				<li>
					<?php echo $this -> Form -> input('password', array('label' => __('Password'), 'before' => '<div class="label-wrapper">', 'between' => '</div>')); ?>
				</li>
			</ul>
		</fieldset>
		<input type="submit" value="Login" class="btn btn-primary">
		<?php echo $this -> Form -> end(); ?>
    	</div>
    	<div class="registration">
	      <?php echo $this -> Form -> create('User', array('action' => 'register')); ?>
	        <fieldset>
	        	<legend><?php echo __('Sign Up');?></legend>
	          <ul class="form-fields unstyled">
	            <li>
	              <div class="label-wrapper">
	                <label for="UserUsername"><?php echo __('User Name') ?></label>
	              </div>
	              <?php echo $this -> Form -> input('username', array('div' => false, 'label' => false, 'after' => $this -> Form -> error('username_unique', 'The username is taken. Please try again.'))); ?>
	            </li>
	            <li>
	              <div class="label-wrapper">
	                <label for="UserNewPassword"><?php echo __('Password') ?></label>
	              </div>              
	              <?php echo $this -> Form -> input('new_password', array('div' => false, 'label' => false, 'type' => 'password')); ?>
	            </li> 
	            <li>
	              <div class="label-wrapper">
	                <label for="UserConfirmPassword"><?php echo __('Confirm Password') ?></label>
	              </div>
	              <?php echo $this -> Form -> input('confirm_password', array('div' => false, 'label' => false, 'type' => 'password')); ?>
	            </li>
	            <li>
	              <div class="label-wrapper">
	                <label for="UserFirstName"><?php echo __('First Name') ?></label>
	              </div>      
	              <?php echo $this -> Form -> input('first_name', array('div' => false, 'label' => false)); ?>
	            </li>
	            <li>
	              <div class="label-wrapper">
	                <label for="UserLastName"><?php echo __('Last Name') ?></label>
	              </div>      
	              <?php echo $this -> Form -> input('last_name', array('div' => false, 'label' => false)); ?>
	            </li> 
	            <li>
	              <div class="label-wrapper">
	                <label for="UserEmail"><?php echo __('Email') ?></label>
	              </div> 
	              <?php echo $this -> Form -> input('email', array('div' => false, 'label' => false)); ?>
	            </li>
	          </ul>
	        </fieldset>
	        <input type="submit" value="Sign Up!" class="btn btn-primary">
	      <?php echo $this -> Form -> end(); ?>
      </div>
	</div>    
  </div>
</div>