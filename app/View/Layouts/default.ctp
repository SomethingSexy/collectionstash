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
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="og:title" content="<?php echo $title_for_layout; ?>">
	<meta name="og:site_name" content="Collection Stash">
	<meta name="og:url" content="<?php echo Router::url($this -> here, true); ?>">
	<?php
	if (isset($description_for_layout)) {
		echo "<meta name='og:description' content='" . $description_for_layout . "' />";
	}
	?>
	<?php
	if (isset($og_image_url)) {
		echo "<meta name='og:image' content='" . $og_image_url . "' />";
	}
	?>
	
	<?php
	echo $this -> Html -> css('thirdparty/bootstrap');
	echo $this -> Minify -> css('thirdparty/datepicker');
	echo $this -> Minify -> css('thirdparty/font-awesome');
	echo $this -> Minify -> css('layout/layout');
	echo $this -> Minify -> css('jquery.treeview');
	echo $this -> Minify -> css('thirdparty/blueimp-gallery');
	echo $this -> Minify -> css('layout/theme');
	echo $this -> Minify -> css('layout/default');

	echo $this -> Minify -> script('thirdparty/json2');
	// There is an issue when I minify this one myself
	echo $this -> Html -> script('thirdparty/underscore');
	echo $this -> Minify -> script('thirdparty/jquery-1.10.1');
	echo $this -> Minify -> script('jquery-ui-1.10.2');
	echo $this -> Minify -> script('thirdparty/bootstrap');
	echo $this -> Minify -> script('thirdparty/bootstrap-datepicker');
	echo $this -> Minify -> script('thirdparty/backbone');
	echo $this -> Minify -> script('thirdparty/backbone.paginator');
	echo $this -> Minify -> script('thirdparty/backbone.validation');
	echo $this -> Minify -> script('thirdparty/typeahead');
	echo $this -> Minify -> script('jquery-plugins');
	echo $this -> Minify -> script('jquery.autocomplete');
	// Replace this with dust eventually
	echo $this -> Minify -> script('thirdparty/tmpl');
	echo $this -> Minify -> script('thirdparty/load-image');
	echo $this -> Minify -> script('thirdparty/canvas-to-blob');

	echo $this -> Minify -> script('thirdparty/dust-full-1.1.1');
	echo $this -> Minify -> script('thirdparty/dust-helpers-1.1.0');
	echo $this -> Minify -> script('cs.dust-helpers');
	?>
	<?php echo $scripts_for_layout; ?>
	

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
<body <?php
if (isset($bodyClass))
	echo 'class="' . $bodyClass . '"';
?>  >
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
			    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".top-nav">
				    <span class="icon-bar"></span>
				    <span class="icon-bar"></span>
				    <span class="icon-bar"></span>
			    </button>
				<a class="navbar-brand" href="#"><img src="/img/icon/add_stash_link_25x25.png"></a>
			</div>
		    <!-- Be sure to leave the brand out there if you want it shown 
		    	
		    	-->
		  
		     
		    <!-- Everything you want hidden at 940px or less, place within here -->
		    <nav class="navbar-collapse collapse top-nav">
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
								<li><?php echo $this -> Html -> link('Account Settings', '/settings', array('escape' => false, 'admin' => false)); ?></li>
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
		<div class="container">
			<div class="row">
				<div class="col-md-12"><?php echo $content_for_layout; ?></div>
			</div>	
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
	<?php
	// list out any modals here that might be common
	echo $this -> element('stash_add_modal');
	echo $this -> element('stash_remove_sold_modal');
	echo $this -> element('stash_edit_listing_modal');
	?>	
	<?php echo $this -> element('sql_dump'); ?>
	
	<!-- The Bootstrap Image Gallery lightbox, should be a child element of the document body -->
	<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-use-bootstrap-modal="false">
	    <!-- The container for the modal slides -->
	    <div class="slides"></div>
	    <!-- Controls for the borderless lightbox -->
	    <h3 class="title"></h3>
	    <a class="prev">‹</a>
	    <a class="next">›</a>
	    <a class="close">×</a>
	    <a class="play-pause"></a>
	    <ol class="indicator"></ol>
	</div>
	<!-- todo - remove all of this once we add requirejs support -->
	<script id="template-stash-add" type="text/x-tmpl">
		<?php echo $this -> element('stash_add'); ?>	
	</script>
	<script id="template-stash-listing-edit" type="text/x-tmpl">
		<?php echo $this -> element('stash_edit_listing'); ?>	
	</script>
	<?php
	echo $this -> Minify -> script('thirdparty/blueimp-gallery');
	echo $this -> Minify -> script('thirdparty/jquery.blueimp-gallery');
	echo $this -> Minify -> script('thirdparty/placeholder');
		?>
	<script>
			! function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (!d.getElementById(id)) {
					js = d.createElement(s);
					js.id = id;
					js.src = "https://platform.twitter.com/widgets.js";
					fjs.parentNode.insertBefore(js, fjs);
				}
			}(document, "script", "twitter-wjs"); 
</script>
	<!-- We are using Font Awesome - http://fortawesome.github.com/Font-Awesome It is AWESOME -->
</body>
</html>
