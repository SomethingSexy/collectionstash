<!DOCTYPE html>
<head>
	<?php echo $this -> Html -> charset();?>
	<title>Collection Stash</title>
	<?php echo $this -> Html -> meta('icon'); ?>
	<link rel="stylesheet" type="text/css" href="/css/layout/index.css" />
	<link rel="stylesheet" type="text/css" href="/css/layout/fluid_bdr.css" />
	<link rel="stylesheet" type="text/css" href="/css/layout/col_3_ml.css" />
	<link rel="stylesheet" type="text/css" href="/css/layout/default.css" />
	<link rel="stylesheet" type="text/css" href="/css/jquery.ui.core.css" />
	<link rel="stylesheet" type="text/css" href="/css/jquery.ui.theme.css" />
	<link rel="stylesheet" type="text/css" href="/css/jquery.ui.dialog.css" />
	<link rel="stylesheet" type="text/css" href="/css/jquery.ui.tabs.css" />
	<link rel="stylesheet" type="text/css" href="/css/redmond.css" />
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
			<div id="header-top">
				<div class="wrapper clearfix">
					<?php
					if($isLoggedIn) {
						echo $username;
					}
					?>
					<div class="box">
						<ul class="nav">

							<?php
							if($isLoggedIn)
							{  ?>
							<li>
								<?php echo $html -> link('Home', array('controller' => 'users', 'action' => 'home'));?>
							</li>
							<?php
							if($isUserAdmin)
							{ ?>
							<li>
								<a class="parent-subnav">Admin</a>
								<ul class="subnav">
									<li>
										<?php echo $html -> link('Pending Submissions', array('action' => 'index', 'controller' => 'adminCollectibles'));?>
									</li>
									<li>
										<a href="#">Sub Nav Link</a>
									</li>
								</ul>
							</li>
							<?php }?>
							<li>
								<?php echo $html -> link('Submit Collectible', array('action' => 'addSelectType', 'controller' => 'collectibles'));?>
							</li>
							<li>
								<?php //echo $html -> link('Users', array('controller' => 'users', 'action' => 'index'));?>
							</li>
							<li>
								<?php echo $html -> link('Logout', array('action' => 'logout', 'controller' => 'users'));?>
							</li>
							<?php  }
								else
								{
   							?>
							<li>
								<?php echo $html -> link('Login', array('controller' => 'users', 'action' => 'login'));?>
							</li>
							<?php if(Configure::read('Settings.registration')){
								echo '<li>';
								echo $html -> link('Register', array('controller'=> 'users', 'action' => 'register'));
								echo '</li>';
								} 
							}?>
						</ul>
					</div>
				</div>
			</div>
			<div id="header-bottom">
				<div class="wrapper">
					<div class="logo">
						<?php echo $html->image('logo/collection_stash_logo_white.png', array('alt' => 'Collection Stash'))?>
					</div>
					<ul class="nav">

					</ul>
				</div>
			</div>
		</div>
		<div id="stage">
			<?php echo $content_for_layout;?>
		</div>
		<div id="footer">
			<span class="version">Version - Alpha</span>
			<!--<span class="links">About | Contact | Donate | Found a bug? </span> -->
			<span class="copyright">Collection Stash - Copyright 2011</span>
		</div>
		<?php /**echo $this->element('sql_dump');
			 echo $js->writeBuffer();
			 */
		?>
		<?php echo $this -> element('sql_dump');?>
</body>
</html>
