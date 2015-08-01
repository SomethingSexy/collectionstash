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
	echo $this -> Html -> css('/bower_components/bootstrap/dist/css/bootstrap');
	echo $this -> Html -> css('/bower_components/bootstrap/dist/css/bootstrap-theme');
	echo $this -> Minify -> css('/bower_components/bootstrap-datepicker/css/datepicker');
	echo $this -> Minify -> css('thirdparty/font-awesome');
	echo $this -> Minify -> css('layout/layout');
	echo $this -> Minify -> css('jquery.treeview');
	echo $this -> Html -> css('/bower_components/blueimp-gallery/css/blueimp-gallery.min');
	echo $this -> Html -> css('/bower_components/toastr/toastr.min');

	echo $this -> Minify -> css('layout/theme');
	echo $this -> Minify -> css('layout/default');

	// There is an issue when I minify this one myself
	echo $this -> Html -> script('/bower_components/underscore/underscore');
	echo $this -> Minify -> script('/bower_components/jquery/dist/jquery');
	// there are still a couple old places using this
	echo $this -> Minify -> script('/bower_components/jquery-ui/jquery-ui.min');
	echo $this -> Minify -> script('/bower_components/bootstrap/dist/js/bootstrap.min');
	echo $this -> Minify -> script('/bower_components/bootstrap-datepicker/js/bootstrap-datepicker');
	echo $this -> Minify -> script('/bower_components/backbone/backbone');
	echo $this -> Minify -> script('jquery-plugins');
	echo $this -> Minify -> script('/bower_components/toastr/toastr');
	// Replace this with dust eventually
	echo $this -> Minify -> script('/bower_components/blueimp-tmpl/js/tmpl.min');
	echo $this -> Minify -> script('/bower_components/blueimp-load-image/js/load-image.all.min');
	echo $this -> Minify -> script('/bower_components/blueimp-canvas-to-blob/js/canvas-to-blob.min');

	echo $this -> Minify -> script('/bower_components/dustjs-linkedin/dist/dust-full');
	echo $this -> Minify -> script('/bower_components/dustjs-linkedin-helpers/dist/dust-helpers');
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
				js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&status=0";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
	</script>
	<div id="wrap">
		<?php echo $this->element('header_menu'); ?>
		<div class="container">
			<div class="row">
				<div class="col-md-12"><?php echo $content_for_layout; ?></div>
			</div>	
		</div>
		<div id="push"></div>
	</div>
	<div id="footer-decorator"><div id="footer-decorator-diagram"></div></div>	
	<footer id="footer">
		<div class="container">
			<div class="row spacer">
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
	</footer>
	<?php
	// list out any modals here that might be common
	echo $this -> element('stash_add_modal');
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
	<?php
	echo $this -> Html -> script('/bower_components/blueimp-gallery/js/blueimp-gallery.min');
	echo $this -> Html -> script('/bower_components/blueimp-gallery/js/jquery.blueimp-gallery.min');
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
