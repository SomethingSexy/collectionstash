<header class="navbar navbar-default navbar-fixed-top" role="navigation">
   <!-- <div class="container"> -->

   		<div class="navbar-header"> 
		    <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
		   <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".top-nav">
			    <span class="fa fa-bars"></span>
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
					echo $this -> Html -> link('My Stash', '/profile/' . $username, array('admin' => false));
					?>
				</li>
				<?php  } ?>
				<?php if(Configure::read('Settings.Collectible.Contribute.allowed')){ ?>
				<li>
					<?php echo $this -> Html -> link('Submit New Collectible', array('admin' => false, 'action' => 'create', 'controller' => 'collectibles')); ?>
				</li>
				<?php } ?>
				<li class="dropdown">
					<?php echo $this -> Html -> link('Collectibles Catalog<i class="fa fa-caret-down"></i>', '#', array('escape' => false, 'class' => 'dropdown-toggle', 'data-toggle' => 'dropdown')); ?>
					
					<ul class="dropdown-menu" role="menu" aria-labelledby="dropdownMenu">
						<li><?php echo $this -> Html -> link('Collectibles', array('admin' => false, 'controller' => 'collectibles', 'action' => 'search')); ?></li>
						<li><a href="/collectibles/catalog?o=o&status=2">Pending Collectibles</a></li>	
						<li><?php echo $this -> Html -> link('Companies', array('admin' => false, 'controller' => 'manufactures', 'action' => 'search')); ?></li>
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
					echo $this -> Html -> link('<i class="fa fa-home"></i>', '/', array('escape' => false, 'admin' => false));
					?>
					</li>
					<?php
					if(isset($isLoggedIn) && $isLoggedIn === true)
					{  ?>
					<li>
						<?php 
							$notificationLinkLabel = '<span class="fa fa-warning"></span>';
							$notificationLinkClass = '';
							if ($notificationsCount !== 0) {
								$notificationLinkLabel .= ' ' . $notificationsCount;
								$notificationLinkClass = 'warning';
							} 
						
							echo $this -> Html -> link($notificationLinkLabel, '/user/home/notifications', array('escape' => false, 'admin' => false, 'class' => $notificationLinkClass)); ?>
					</li>		
					<li class="dropdown">
						<?php echo $this -> Html -> link('<i class="fa fa-user"></i><i class="fa fa-caret-down"></i>', '/profiles', array('escape' => false, 'admin' => false, 'class' => 'dropdown-toggle', 'data-toggle' => 'dropdown')); ?>
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
						<?php echo $this -> Html -> link('<i class="fa fa-cog"></i>', '/admin/collectibles', array('escape' => false, 'admin' => true)); ?>
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