<?php echo $this -> Html -> script('home', array('inline' => false));?>
<div id="home-components">
	<div class="component welcome-component">
		<div class="inside" >
			<div class="component-view">	
				<?php echo $this -> Html -> image('logo/collection_stash_logo_white.png', array('alt' => 'Collection Stash'));?>
			
					<p class="heading">Welcome to Collection Stash</p>
   					<p class="body"> This site was designed to provide collectors with the ability to record and catalogue their prized possessions and connect with others who share a similar passion.  By becoming a member, you can track all details of items in your collection: manufacturer, artist, purchase date, edition size, and more.   We welcome your feedback on the site and look forward to making improvements in the future.</p>
			
			</div>
		</div>
	</div>
	<div class="site-information">
		<ul class="information">
			<li class="submit">
				<span class="title"><?php echo __('Submit');?></span>
				<p><?php echo __('Help build the largest collectible database and community by submitting new collectibles you own or enjoy.')?></p>
			</li>
			<li class="stash">
				<span class="title"><?php echo __('Stash');?></span>
				<p><?php echo __('Add collectibles from our growing database to your stash to build your collection.')?></p>
			</li>	
			<li class="share">
				<span class="title"><?php echo __('Share');?></span>
				<p><?php echo __('Share your stash with the community and friends.')?></p>
			</li>			
		</ul>			
		<ul class="buttons">
			<li>
				<a href="/collectibles/catalog"><?php echo __('Discover'); ?></a>
				
			</li>
			<li>
				<a href="/users/register"><?php echo __('Register'); ?></a>
			</li>
		</ul>
	</div>
</div>