<div id="my-stashes-component" class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $stashUsername . '\'s' .__(' stash', true)
			?></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-view">
			<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
				<ul class="ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
					<li class="ui-state-default ui-corner-top ui-tabs-selected ui-state-active">
						<a href="#collectibles"><?php echo __('Collectibles');?></a>
					</li>
					<li class="ui-state-default ui-corner-top">
						<a href="#photos"><?php echo __('Photos');?></a>
					</li>
				</ul>
				<div id="collectibles" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
					<div class="actions icon">
						<ul>
							<?php
							if (isset($myStash) && $myStash) {
								echo '<li><a title="Add Collectibles" class="link add-stash-link" href="/collectibles/search"><img src="/img/icon/add_stash_link_25x25.png"/></a></li>';
								echo '<li>';
								echo '<a title="Edit" class="link glimpse-link" href="/stashs/view/' . $stashUsername . '/tiles"><img src="/img/icon/pencil.png"/></a>';
								echo '</li>';
							}
							?>
							<li>
								<?php echo '<a title="Photo Gallery" class="link detail-link" href="/stashs/view/' . $stashUsername . '"><img src="/img/icon/photos.png"/></a>';?>
							</li>

						</ul>
					</div>