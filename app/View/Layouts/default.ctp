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
	echo $this -> Minify -> css('css/layout/reset');
	echo $this -> Minify -> css('css/thirdparty/bootstrap');
	echo $this -> Minify -> css('css/thirdparty/bootstrap-responsive');
	echo $this -> Minify -> css('css/thirdparty/datepicker');
	echo $this -> Minify -> css('css/thirdparty/font-awesome');
	echo $this -> Minify -> css('css/layout/layout');
	echo $this -> Minify -> css('css/jquery.ui.core');
	echo $this -> Minify -> css('css/jquery.ui.theme');
	echo $this -> Minify -> css('css/jquery.ui.dialog');
	echo $this -> Minify -> css('css/jquery.ui.tabs');
	echo $this -> Minify -> css('css/jquery.treeview');
	echo $this -> Minify -> css('css/thirdparty/bootstrap-image-gallery');
	echo $this -> Minify -> css('css/layout/default');

	echo $this -> Minify -> script('js/jquery-1.7');
	echo $this -> Minify -> script('js/jquery-ui-1.8.18');
	echo $this -> Minify -> script('js/jquery-plugins');
	echo $this -> Minify -> script('js/jquery.autocomplete');
	echo $this -> Minify -> script('js/thirdparty/tmpl');
	echo $this -> Minify -> script('js/thirdparty/load-image');
	echo $this -> Minify -> script('js/thirdparty/canvas-to-blob');
	echo $this -> Minify -> script('js/thirdparty/bootstrap');
	echo $this -> Minify -> script('js/thirdparty/bootstrap-datepicker');
	echo $this -> Minify -> script('js/thirdparty/json2');
	?>
	<?php echo $scripts_for_layout; ?>
	
	
	<script>
		$(function() {
			$("#login-dialog").dialog({
				'autoOpen' : false,
				'width' : 'auto',
				'height' : 'auto',
				'resizable' : false,
				'modal' : true,
				'buttons' : [{
					'text' : 'Login',
					'class' : 'btn btn-primary',
					"click" : function() {
						$('#UserLoginFormDialog').submit();
					}
				}]

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
    <div class="navbar navbar-fixed-top">
	    <div class="navbar-inner">
		    <div class="container">
		     
			    <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
			    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				    <span class="icon-bar"></span>
				    <span class="icon-bar"></span>
				    <span class="icon-bar"></span>
			    </a>
			     
				<?php
				if (isset($isLoggedIn) && $isLoggedIn === true) {
					echo '<a class="brand" href="#">' . __('Welcome, ') . $username . '</a>';
				}
				?>
			    <!-- Be sure to leave the brand out there if you want it shown 
			    	
			    	-->
			  
			     
			    <!-- Everything you want hidden at 940px or less, place within here -->
			    <div class="nav-collapse collapse">
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
							<?php echo $this -> Html -> link('Submit New Collectible', array('admin' => false, 'action' => 'addSelectType', 'controller' => 'collectibles')); ?>
						</li>
						<?php } ?>
						<li>
							<?php echo $this -> Html -> link('Tags', array('admin' => false, 'controller' => 'tags')); ?>
						</li>
						<li>
							<?php echo $this -> Html -> link('Community', array('admin' => false, 'controller' => 'users', 'action' => 'index')); ?>
						</li>
						<li class="dropdown">
							<?php echo $this -> Html -> link('Catalog<b class="caret"></b>', '#', array('escape' => false, 'class' => 'dropdown-toggle', 'data-toggle' => 'dropdown')); ?>
							
							<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
								<li><?php echo $this -> Html -> link('Collectibles', array('admin' => false, 'controller' => 'collectibles', 'action' => 'search')); ?></li>
								<li><?php echo $this -> Html -> link('Collectible Parts', array('admin' => false, 'controller' => 'attributes', 'action' => 'index')); ?></li>
							</ul>
						</li>
						<li>
							<a href="/comments/"><?php echo __('Discussion') . '<span class="beta inner-text">Beta</span>'; ?></a>
						</li>									
					</ul>
					<form method="get" class="navbar-search" action="/collectibles/search">
						<input id="q" type="text" name="q" class="search-query" placeholder="Search">
					</form>
					<ul class="nav pull-right">
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
	    </div>
	</div>
	<header class="header"></header>
	<div class="container">

		<div class="row">

			<div class="span12">
					<?php echo $content_for_layout; ?>

			</div>

		
		</div>

</div>
<footer class="footer">
		
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
			<ul class="links unstyled">
				<li>&copy; Collection Stash</li>
			</ul>
		
	
</footer>
		<?php /**echo $this->element('sql_dump');
	 echo $js->writeBuffer();
	 */
		?>
		<?php echo $this -> element('sql_dump'); ?>

		<div id="login-dialog" class="dialog" title="Login">
			<div class="component component-dialog">
				<div class="inside" >
					<div class="component-view">
						<?php echo $this -> Form -> create('User', array('action' => 'login', 'id' => 'UserLoginFormDialog')); ?>
						<fieldset>
							<ul class="form-fields dialog-fields unstyled">
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
		
		<div id="modal-gallery" class="modal modal-gallery hide fade" tabindex="-1">
		    <div class="modal-header">
		        <a class="close" data-dismiss="modal">&times;</a>
		        <h3 class="modal-title"></h3>
		    </div>
		    <div class="modal-body"><div class="modal-image"></div></div>
		    <div class="modal-footer">
		        <a class="btn btn-primary modal-next">Next <i class="icon-arrow-right icon-white"></i></a>
		        <a class="btn btn-info modal-prev"><i class="icon-arrow-left icon-white"></i> Previous</a>
		        <a class="btn btn-success modal-play modal-slideshow" data-slideshow="5000"><i class="icon-play icon-white"></i> Slideshow</a>
		        <a class="btn modal-download" target="_blank"><i class="icon-download"></i> Download</a>
		    </div>
		</div>
		
		<?php
		echo $this -> Minify -> script('js/thirdparty/bootstrap-image-gallery');
		?>
		<!-- We are using Font Awesome - http://fortawesome.github.com/Font-Awesome It is AWESOME -->
</body>
</html>
