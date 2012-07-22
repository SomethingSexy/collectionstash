<!DOCTYPE html>
<head>
	<?php echo $this -> Html -> charset(); ?>
	<title><?php echo $title_for_layout
		?></title>
	<?php echo $this -> Html -> meta('icon'); ?>
	<?php
	if (isset($description_for_layout)) { echo "<meta name='description' content='" . $description_for_layout . "' />";
	}
	?>
	<?php
	if (isset($keywords_for_layout)) { echo "<meta name='keywords' content='" . $keywords_for_layout . "' />";
	}
	?>
	<?php
	$this -> Minify -> css(array('css/layout/index'));
	$this -> Minify -> css(array('css/layout/layout'));
	$this -> Minify -> css(array('css/jquery.ui.core'));
	$this -> Minify -> css(array('css/jquery.ui.theme'));
	$this -> Minify -> css(array('css/jquery.ui.dialog'));
	$this -> Minify -> css(array('css/jquery.ui.tabs'));
	$this -> Minify -> css(array('css/jquery.treeview'));
	$this -> Minify -> css(array('css/jquery.fancybox'));
	$this -> Minify -> css(array('css/redmond'));
	$this -> Minify -> css(array('css/layout/default'));
	$this -> Minify -> css(array('css/layout/non_msie'));

	//$this -> Minify -> js(array('js/es5-shim'));
	$this -> Minify -> js(array('js/jquery-1.7'));
	$this -> Minify -> js(array('js/jquery-ui-1.8.18'));
	$this -> Minify -> js(array('js/jquery-plugins'));
	$this -> Minify -> js(array('js/jquery.autocomplete'));
	$this -> Minify -> js(array('js/jquery.fancybox'));
	?>
	<?php echo $scripts_for_layout; ?>
	<script>
		$(function() {

			// $("ul.subnav").parent().append("<span>B</span>"); //Only shows drop down trigger when js is enabled (Adds empty span tag after ul.subnav*)

			$("ul.nav .parent-subnav").click(function() {//When trigger is clicked...
				$(this).parent('li').addClass('selected');
				//Following events are applied to the subnav itself (moving subnav up and down)
				$(this).parent().find("ul.subnav").toggle(0, function() {

					$(this).parent('li').toggleClass('selected', $(this).is(":visible"));
				});
				//Drop down the subnav on click
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
			$("#login-dialog").dialog({
				'autoOpen' : false,
				'width' : 550,
				'height' : 'auto',
				'resizable' : false,
				'modal' : true,
				'buttons' : {
					"Login" : function() {
						$('#UserLoginForm').submit();
					}
				}

			});

			$('#login-link').click(function() {
				$('#UserUsername').val('');
				$('#UserPassword').val('');
				$('#UserLoginForm').find('#UserUsername').next('div.error-message').remove();
				$('#login-dialog').dialog('open');
				return false;
			});
			$('#q').focus(function() {
				var firstTime = $('#q').data('firstTime');
				if ( typeof firstTime === "undefined") {
					$('#q').val('');
					$('#q').data('firstTime', false);
				}
			});

		});
	</script>
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-25703659-1']);
		_gaq.push(['_trackPageview']);

		(function() {
			var ga = document.createElement('script');
			ga.type = 'text/javascript';
			ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(ga, s);
		})();
	</script>
</head>
<body>
	<div id="fb-root"></div>
	<script>
		( function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id))
					return;
				js = d.createElement(s);
				js.id = id;
				js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
	</script>

	<div id="header" class="clearfix">
		<div class="header-container">
			<div class="header title">
				<h1><?php echo __('Collection Stash'); ?></h1>
				<span class="sub-title"><?php echo __('Are you squirreling?'); ?></span>
			</div>
			<div class="header navigation">
				<div class="box">
					<ul class="nav">
						<?php
						if (isset($isLoggedIn) && $isLoggedIn === true) {
							echo '<li>';
							echo __('Welcome, ');
							echo $username . '!';
							echo '</li>';
						}
						?>
						<li>
							<?php echo $this -> Html -> link('Home', array('admin' => false, 'controller' => '/')); ?>
						</li>
						<?php
						if(isset($isLoggedIn) && $isLoggedIn === true)
						{  ?>

						<li>
							<?php echo $this -> Html -> link('Profile', array('admin' => false, 'controller' => 'profiles')); ?>
						</li>
						<?php
						if($isUserAdmin)
						{ ?>
						<li>
							<?php echo $this -> Html -> link('Admin', array('admin' => true, 'action' => 'index', 'controller' => 'collectibles')); ?>
						</li>
						<?php } ?>
						<li>
							<?php echo $this -> Html -> link('Logout', array('admin' => false, 'action' => 'logout', 'controller' => 'users')); ?>
						</li>
						<?php  }
							else
							{
						?>
						<li>
							<a id='login-link' href="/users/login"><?php echo __('Login'); ?></a>
						</li>
						<?php
						if (Configure::read('Settings.registration.open')) {
							echo '<li>';
							echo $this -> Html -> link('Register', array('controller' => 'users', 'action' => 'register'));
							echo '</li>';
						}
						}
						?>
					</ul>
				</div>
			</div>
			<div class="site-search">
				<form method="get" action="/collectibles/search">
					<input id="q" type="text" name="q" value="Find a Collectible">
				</form>
			</div>
		</div>
	</div>
	<div id="container">

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
								echo $this -> Html -> link('My Stash', array('admin' => false, 'controller' => 'stashs', 'action' => 'view', $username));
								?>
							</li>
							<?php  } ?>
							<?php if(Configure::read('Settings.Collectible.Contribute.allowed')){ ?>
							<li>
								<?php echo $this -> Html -> link('Contribute', array('admin' => false, 'action' => 'addSelectType', 'controller' => 'collectibles')); ?>
							</li>
							<?php } ?>
							<li>
								<?php echo $this -> Html -> link('Tags', array('admin' => false, 'controller' => 'tags')); ?>
							</li>
							<li>
								<?php echo $this -> Html -> link('Community', array('admin' => false, 'controller' => 'users', 'action' => 'index')); ?>
							</li>
							<li>
								<?php echo $this -> Html -> link('Catalog', array('admin' => false, 'controller' => 'collectibles', 'action' => 'search')); ?>
							</li>
							<li>
								<a href="/comments/"><?php echo __('Discussion') . '<span class="beta inner-text">Beta</span>'; ?></a>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<?php echo $content_for_layout; ?>

		</div>
		<div id="footer">
			<div class="logo">
				<?php //echo $html->image('logo/collection_stash_logo_white.png', array('alt' => 'Collection Stash')) ?>
			</div>
			<div class="social">
				<div>
					<a href="http://www.twitter.com/collectionstash"><img src="http://twitter-badges.s3.amazonaws.com/t_logo-a.png" alt="Follow collectionstash on Twitter"/></a>
				</div>
				<div>
					<div class="fb-like" data-href="http://www.facebook.com/pages/Collection-Stash/311656598850547" data-send="true" data-layout="button_count" data-width="125" data-show-faces="false"></div>
				</div>
				<div>
					
				</div>
				<!--<li>About Us</li>
				<li>Contact Us</li>
				<li>Donate</li>
				<li>Report a Bug</li>
				<li>Milestones</li>
				<li>Request a Feature</li>-->
			</div>
			<ul class="links">
				<li>&copy; Collection Stash</li>
			</ul>
		</div>

		<?php /**echo $this->element('sql_dump');
	 echo $js->writeBuffer();
	 */
		?>
		<?php echo $this -> element('sql_dump'); ?>

		<div id="login-dialog" class="dialog" title="Login">
			<div class="component component-dialog">
				<div class="inside" >
					<div class="component-view">
						<?php echo $this -> Form -> create('User', array('action' => 'login')); ?>
						<fieldset>
							<ul class="form-fields dialog-fields">
								<li>
									<div class="input text">
										<div class="label-wrapper">
											<label for="UserUsername"><?php echo __('Username'); ?></label>
										</div>
										<input id="UserUsername" type="text" maxlength="50" name="data[User][username]">
									</div>
								</li>
								<li>
									<?php echo $this -> Form -> input('password', array('label' => __('Password'), 'before' => '<div class="label-wrapper">', 'between' => '</div>')); ?>
								</li>
							</ul>
							<?php
							if (isset($request_params)) {
								echo $this -> Form -> hidden('fromPage', array('value' => $this -> here . $request_params));
							} else {
								echo $this -> Form -> hidden('fromPage', array('value' => $this -> here));
							}
							?>
						</fieldset>
						<?php echo $this -> Form -> end(); ?>
					</div>
				</div>
			</div>
		</div>
</body>
</html>
