<!DOCTYPE html>
<head>
	<?php echo $this -> Html -> charset();?>
	<title><?php echo $title_for_layout ?></title>
	<?php echo $this -> Html -> meta('icon'); ?>
	<?php if(isset($description_for_layout)){ echo "<meta name='description' content='".$description_for_layout."' />"; } ?>
	<?php if(isset($keywords_for_layout)){ echo "<meta name='keywords' content='".$keywords_for_layout."' />"; } ?>
	<link rel="stylesheet" type="text/css" href="/css/layout/index.css" />
	<link rel="stylesheet" type="text/css" href="/css/layout/layout.css" />
	<link rel="stylesheet" type="text/css" href="/css/jquery.ui.core.css" />
	<link rel="stylesheet" type="text/css" href="/css/jquery.ui.theme.css" />
	<link rel="stylesheet" type="text/css" href="/css/jquery.ui.dialog.css" />
	<link rel="stylesheet" type="text/css" href="/css/jquery.ui.tabs.css" />
	<link rel="stylesheet" type="text/css" href="/css/redmond.css" />
	<link rel="stylesheet" type="text/css" href="/css/layout/default.css" />
	<link rel="stylesheet" type="text/css" href="/css/layout/non_msie.css" />
	<script type="text/javascript" src="/js/jquery-1.6.1.js"></script>
	<script type="text/javascript" src="/js/jquery-ui-1.8.5.js"></script>
	<script type="text/javascript" src="/js/jquery-plugins.js"></script>
	<?php echo $scripts_for_layout; ?>
	<script>
		$( function() {

			// $("ul.subnav").parent().append("<span>B</span>"); //Only shows drop down trigger when js is enabled (Adds empty span tag after ul.subnav*)

			$("ul.nav .parent-subnav").click( function() { //When trigger is clicked...
				$(this).parent('li').addClass('selected');
				//Following events are applied to the subnav itself (moving subnav up and down)
				$(this).parent().find("ul.subnav").toggle(0, function() {

					$(this).parent('li').toggleClass('selected', $(this).is(":visible"));
				}); //Drop down the subnav on click
				/* $(this).parent().hover( function() {

				}, function() {
				//When the mouse hovers out of the subnav, move it back up
				$(this).parent().find("ul.subnav").hide(function(){
				//once done, remove the select class
				$(this).parent('li').removeClass('selected');
				});

				});*/
				//Following events are applied to the trigger (Hover events for the trigger)
			});
			//.hover( function() {
			// $(this).addClass("subhover"); //On hover over, add class "subhover"
			//}, function() {	//On Hover Out
			// $(this).removeClass("subhover"); //On hover out, remove class "subhover"
			//});
			$( "#login-dialog" ).dialog({
				'autoOpen' : false,
				'width' : 500,
				'height': 300,
				'resizable': false,
				'modal': true,
				'buttons': {
					"Login": function() {
						$('#UserLoginForm').submit();
					}
				}
		
			});	
			
			$('#login-link').click(function(){
				$('#UserUsername').val('');
				$('#UserPassword').val('');
				$('#UserLoginForm').find('#UserUsername').next('div.error-message').remove();
				$('#login-dialog').dialog('open');
				return false;
			});		
		});
		//TODO I don't like this here
		$( function() {
			$('.form-fields li input').focus( function() {
				$(this).parent('li').addClass('focused');
			});
			$('.form-fields li input').blur( function() {
				$(this).parent('li').removeClass('focused');
			});
		});
	</script>
</head>
<body>
	<div id="container">
		<div id="header" class="clearfix">
			<div class="header title">
				<h1><?php __('Collection Stash'); ?></h1>
			</div>
			<div class="header navigation">
				<div class="box">
					<ul class="nav">
							<li>
								<?php echo $html -> link('Home', array('controller' => '/'));?>
							</li>
							<?php
							if(isset($isLoggedIn) && $isLoggedIn === true)
							{  ?>

							<li>
								<?php echo $html -> link('Account', array('controller' => 'profiles'));?>
							</li>
							<?php
							if($isUserAdmin)
							{ ?>
								<li>
									<?php echo $html -> link('Admin', array('action' => 'index', 'controller' => 'adminCollectibles'));?>
								</li>
							<?php }?>
							<li>
								<?php echo $html -> link('Logout', array('action' => 'logout', 'controller' => 'users'));?>
							</li>
							<?php  }
								else
								{
   							?>
							<li>
								<a id='login-link' href="/users/login"><?php __('Login');?></a>
							</li>
							<?php if(Configure::read('Settings.registration.open')){
								echo '<li>';
								echo $html -> link('Register', array('controller'=> 'users', 'action' => 'register'));
								echo '</li>';
								} 
							}?>					
					</ul>
				</div>	
			</div>
		</div>
		<div id="stage">
			<div class="main-navigation">
				<div class="inside">
					<div class="box">
						<ul class="nav">
							<?php
							if(isset($isLoggedIn) && $isLoggedIn === true)
							{  ?>
							<li>
								<?php 
									$user = $session->read('user');
									echo $html -> link('My Stash', array('controller' => 'stashs', 'action' => 'view', $user['User']['username'], 'view'=>'glimpse'));
								?>
							</li>
							<?php  }
   							?>
   							<li>
								<?php echo $html -> link('Contribute', array('action' => 'addSelectType', 'controller' => 'collectibles'));?>
							</li>
   							<li>
								<?php echo $html -> link('Tags', array('controller' => 'tags'));?>
							</li>
						</ul>
						<div class="site-search">
							<form method="get" action="/collectibles/search">
								<input id="q" type="text" name="q">
							</form>								
						</div>
					</div>
				</div>
			</div>
			<?php echo $content_for_layout;?>
			
			
		</div>	
		<div id="footer">					
			<div class="logo">
				<?php echo $html->image('logo/collection_stash_logo_white.png', array('alt' => 'Collection Stash'))?>
			</div>
			<span class="links">
				<ul>
					<li>&copy; Collection Stash</li>
					<li>About Us</li>
					<li>Contact Us</li>
					<li>Donate</li>
					<li>Report a Bug</li>
					<li>Milestones</li>
					<li>Request a Feature</li>
					<li><a href="http://www.twitter.com/collectionstash"><img src="http://twitter-badges.s3.amazonaws.com/t_logo-a.png" alt="Follow collectionstash on Twitter"/></a></li>
				</ul>
			</span>
			
	</div>	
	</div>
	
		<?php /**echo $this->element('sql_dump');
			 echo $js->writeBuffer();
			 */
		?>
		<?php echo $this -> element('sql_dump');?>
		
<div id="login-dialog" class="dialog" title="Login">
  <div class="component component-dialog">
    <div class="inside" >
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
          <?php 
          	if(isset($request_params)){
          		echo $form->hidden('fromPage', array('value'=> $this->here.$request_params)); 	
          	} else {
          		echo $form->hidden('fromPage', array('value'=> $this->here)); 
          	}	
          	
          ?>
        </fieldset>
      <?php echo $form->end();?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
