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
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<?php
	echo $this -> Html -> css('thirdparty/bootstrap');
	echo $this -> Minify -> css('css/thirdparty/datepicker');
	echo $this -> Minify -> css('css/thirdparty/font-awesome');
	echo $this -> Minify -> css('css/layout/layout');
	echo $this -> Minify -> css('css/jquery.ui.core');
	echo $this -> Minify -> css('css/jquery.ui.theme');
	echo $this -> Minify -> css('css/jquery.ui.dialog');
	echo $this -> Minify -> css('css/jquery.ui.tabs');
	echo $this -> Minify -> css('css/jquery.treeview');
	echo $this -> Minify -> css('css/thirdparty/blueimp-gallery');
	echo $this -> Minify -> css('css/thirdparty/bootstrap-image-gallery');
	echo $this -> Minify -> css('css/layout/theme');
	echo $this -> Minify -> css('css/layout/default');

	echo $this -> Minify -> script('js/thirdparty/json2');
	// There is an issue when I minify this one myself
	echo $this -> Html -> script('thirdparty/underscore');
	echo $this -> Minify -> script('js/thirdparty/jquery-1.10.1');
	echo $this -> Minify -> script('js/jquery-ui-1.10.2');
	echo $this -> Minify -> script('js/thirdparty/bootstrap');
	echo $this -> Minify -> script('js/thirdparty/bootstrap-datepicker');
	echo $this -> Minify -> script('js/thirdparty/backbone');
	echo $this -> Minify -> script('js/thirdparty/backbone.paginator');
	echo $this -> Minify -> script('js/thirdparty/backbone.validation');
	echo $this -> Minify -> script('js/thirdparty/typeahead');
	echo $this -> Minify -> script('js/jquery-plugins');
	echo $this -> Minify -> script('js/jquery.autocomplete');
	// Replace this with dust eventually
	echo $this -> Minify -> script('js/thirdparty/tmpl');
	echo $this -> Minify -> script('js/thirdparty/load-image');
	echo $this -> Minify -> script('js/thirdparty/canvas-to-blob');
	echo $this -> Minify -> script('js/thirdparty/dust-full-1.1.1');
	echo $this -> Html -> script('thirdparty/dust-helpers-1.1.0');
	echo $this -> Html -> script('cs.dust-helpers');
	?>
	<?php echo $scripts_for_layout; ?>
	
	
	<script>
		$(function() {
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
	<div id="wrap">
	    <header class="navbar navbar-default navbar-fixed-top" role="navigation">
		   <!-- <div class="container"> -->
		
		   		<div class="navbar-header"> 
				    <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
				   <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
					    <span class="icon-bar"></span>
					    <span class="icon-bar"></span>
					    <span class="icon-bar"></span>
				    </button>
			     
					<?php
					if (isset($isLoggedIn) && $isLoggedIn === true) {
						echo '<a class="navbar-brand" href="#"><img src="/img/icon/add_stash_link_25x25.png"> ' . __('Welcome, ') . $username . '</a>';
					} else {
						echo '<a class="navbar-brand" href="#"><img src="/img/icon/add_stash_link_25x25.png"></a>';
					}
					?>
				</div>
			    <!-- Be sure to leave the brand out there if you want it shown 
			    	
			    	-->
			  
			     
			    <!-- Everything you want hidden at 940px or less, place within here -->
			    <nav class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
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
							<?php echo $this -> Html -> link('Submit New Collectible', array('admin' => false, 'action' => 'create', 'controller' => 'collectibles')); ?>
						</li>
						<?php } ?>
						<li class="dropdown">
							<?php echo $this -> Html -> link('Collectibles Catalog<i class="icon-caret-down"></i>', '#', array('escape' => false, 'class' => 'dropdown-toggle', 'data-toggle' => 'dropdown')); ?>
							
							<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
								<li><?php echo $this -> Html -> link('Collectibles', array('admin' => false, 'controller' => 'collectibles', 'action' => 'search')); ?></li>
								<li><?php echo $this -> Html -> link('Collectible Parts', array('admin' => false, 'controller' => 'attributes', 'action' => 'index')); ?></li>
							</ul>
						</li>
						<li>
							<?php echo $this -> Html -> link('Community', array('admin' => false, 'controller' => 'users', 'action' => 'index')); ?>
						</li>
						<li>
							<?php echo $this -> Html -> link('User Gallery', array('admin' => false, 'controller' => 'user_uploads', 'action' => 'gallery')); ?>
						</li>
						<li>
							<a href="/comments/"><?php echo __('Discussion'); ?></a>
						</li>									
					</ul>
					<form method="get" class="navbar-form navbar-left" role="search" action="/collectibles/search">
						<input id="q" type="text" name="q" class="search-query form-control col-lg-8" placeholder="Search">
					</form>
					<ul class="nav navbar-nav navbar-right account">
							<li>
							<?php
							$homeLinkLabel = '<i class="icon-home"></i>';
							$homeLinkClass = '';
							if ($notificationsCount !== 0) {
								$homeLinkLabel .= ' ' . $notificationsCount;
								$homeLinkClass = 'warning';
							}
							echo $this -> Html -> link($homeLinkLabel, '/', array('escape' => false, 'admin' => false, 'class' => $homeLinkClass));
							?>
							</li>
							<?php
							if(isset($isLoggedIn) && $isLoggedIn === true)
							{  ?>
		
							<li class="dropdown">
								<?php echo $this -> Html -> link('<i class="icon-user"></i><i class="icon-caret-down"></i>', '/profiles', array('escape' => false, 'admin' => false, 'class' => 'dropdown-toggle', 'data-toggle' => 'dropdown')); ?>
								<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
									<li><?php echo $this -> Html -> link('Account Settings', '/profiles', array('escape' => false, 'admin' => false)); ?></li>
									<li><a target="_blank" href="/pages/collection_stash_documentation" class="">Help</a></li>
									<li class="divider"></li>
									<li><?php echo $this -> Html -> link('Logout', array('admin' => false, 'action' => 'logout', 'controller' => 'users')); ?></li>
								</ul>
							</li>
							<?php
							if($isUserAdmin)
							{ ?>
							<li>
								<?php echo $this -> Html -> link('<i class="icon-cog"></i>', '/admin/collectibles', array('escape' => false, 'admin' => true)); ?>
							</li>
							<?php } ?>
	
							<?php  }
		else
		{
								?>
							<li>
								<a href="/users/login"><?php echo __('Login'); ?></a>
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
			    </nav>
		</header>
		<div class="row">
			<?php echo $content_for_layout; ?>
		</div>	
	</div>
	<footer>
		<div id="footer">
			<div class="container narrow">
				<div class="row spacer">
					<div class="col-md-12">
						<div class="row">
							<div class="col-md-6">
								<div class="social">
									<div>
										<a href="http://www.twitter.com/collectionstash"><img src="http://twitter-badges.s3.amazonaws.com/t_logo-a.png" alt="Follow collectionstash on Twitter"/></a>
									</div>
									<div>
										<div class="fb-like" data-href="http://www.facebook.com/pages/Collection-Stash/311656598850547" data-send="true" data-layout="button_count" data-width="125" data-show-faces="false"></div>
									</div>
								</div>					
							</div>
							<div class="col-md-6">
								<ul class="links list-unstyled pull-right">
									<li>&copy; Collection Stash <a href="/pages/change_log">v<?php echo Configure::read('Settings.version'); ?></a></li>
								</ul>
							</div>	
						</div>
						<div class="row spacer">
							<div class="col-md-12">
								<p>All Images & Characters contained within this site are copyright and trademark their respective owners.  No portion of this web site, including the images contained herein, may be reproduced without the express written permission of the appropriate copyright & trademark holder.</p>
								<p>Original logo created by Bamboota.  Artwork created by Devil_666.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>		
	</footer>

	<script id="template-stash-add" type="text/x-tmpl">
		<?php echo $this -> element('stash_add'); ?>	
	</script>
	<script id="template-stash-remove" type="text/x-tmpl">
		<?php echo $this -> element('stash_remove'); ?>	
	</script>
	
	<?php
	// list out any modals here that might be common
	echo $this -> element('stash_add_modal');
	echo $this -> element('stash_remove_modal');
	?>	
		<?php echo $this -> element('sql_dump'); ?>
	<!-- The Bootstrap Image Gallery lightbox, should be a child element of the document body -->
	<div id="blueimp-gallery" class="blueimp-gallery">
	    <!-- The container for the modal slides -->
	    <div class="slides"></div>
	    <!-- Controls for the borderless lightbox -->
	    <h3 class="title"></h3>
	    <a class="prev">‹</a>
	    <a class="next">›</a>
	    <a class="close">×</a>
	    <a class="play-pause"></a>
	    <ol class="indicator"></ol>
	    <!-- The modal dialog, which will be used to wrap the lightbox content -->
	    <div class="modal fade">
	        <div class="modal-dialog">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <button type="button" class="close" aria-hidden="true">&times;</button>
	                    <h4 class="modal-title"></h4>
	                </div>
	                <div class="modal-body next"></div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-default pull-left prev">
	                        <i class="glyphicon glyphicon-chevron-left"></i>
	                        Previous
	                    </button>
	                    <button type="button" class="btn btn-primary next">
	                        Next
	                        <i class="glyphicon glyphicon-chevron-right"></i>
	                    </button>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>
		<?php
		echo $this -> Minify -> script('js/thirdparty/blueimp-gallery');
		echo $this -> Minify -> script('js/thirdparty/jquery.blueimp-gallery');
		echo $this -> Minify -> script('js/thirdparty/bootstrap-image-gallery');
		echo $this -> Minify -> script('js/thirdparty/placeholder');
			?>
		<!-- We are using Font Awesome - http://fortawesome.github.com/Font-Awesome It is AWESOME -->
</body>
</html>
