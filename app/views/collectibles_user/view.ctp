<div class="component" id="collectible-detail">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $stashUsername . '\'s' .__(' collectible', true)
			?></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-view">
			<div class="collectible item">
				<div class="collectible image">
					<?php
					if (!empty($collectible['Collectible']['Upload'])) {
					?>
					<?php echo $fileUpload -> image($collectible['Collectible']['Upload'][0]['name'], array('width' => '100'));?>
					<div class="collectible image-fullsize hidden">
						<?php echo $fileUpload -> image($collectible['Collectible']['Upload'][0]['name'], array('width' => 0));?>
					</div>
					<?php } else {?><img src="/img/silhouette_thumb.png"/>
					<?php }?>
				</div>
				<div class="collectible detail">
					<dl>
						<dt>
							<?php __('Date Added');?>
						</dt>
						<dd>
							<?php echo $collectible['CollectiblesUser']['created'];?>
						</dd>
						<?php
						$editionSize = $collectible['Collectible']['edition_size'];
						if($collectible['Collectible']['showUserEditionSize'])
						{
						?>

						<dt>
							<?php __('Edition Size');?>
						</dt>
						<dd>
							<?php echo $collectible['CollectiblesUser']['edition_size'] . '/' . $collectible['Collectible']['edition_size'];?>
						</dd>
						<?php }?>
						<dt>
							<?php __('Cost');?>
						</dt>
						<dd>
							<?php echo '$' . $collectible['CollectiblesUser']['cost'];?>
						</dd>
						<?php
							if (isset($collectible['Condition']) && !empty($collectible['Condition'])) {
								echo '<dt>';
								echo __('Condition');
								echo '</dt>';
								echo '<dd>';
								echo $collectible['Condition']['name'];
								echo '</dd>';
							}
						?>
						<?php
							if (isset($collectible['Merchant']) && !empty($collectible['Merchant'])) {
								echo '<dt>';
								echo __('Purchased From');
								echo '</dt>';
								echo '<dd>';
								echo $collectible['Merchant']['name'];
								echo '</dd>';
							}
						?>
					</dl>
				</div>
			</div>
		</div>
	</div>
</div>
