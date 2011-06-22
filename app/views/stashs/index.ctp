<?php echo $this -> Html -> script('collectible-list', array('inline' => false));?>
<div class="component" id="collectibles-list-component">
	<div class="inside" >
		<div class="component-title">
			<h2>
			<?php __('My Stash');?>
			</h2>
		</div>
		<?php echo $this->element('flash'); ?>
		<div class="component-info">
			<div>
				You have <?php echo $collectibleCount ?> collectibles in this stash. <?php echo $html -> link('Add to Stash', array('controller' => 'collections', 'action' => 'addSearch', 'stashId' => $this -> Session -> read('stashId')));?>
			</div>
		</div>
		<div class="component-view">
			<div class="collectibles view">
				<?php
				foreach ($collectibles as $collectible):
				?>
				<div class="collectible item">
					<?php echo $this -> element('collectible_list_image', array(
						'collectible' => $collectible
					));?>
					<?php echo $this -> element('collectible_list_detail', array(
						'collectible' => $collectible['Collectible'],
						'manufacture' => $collectible['Collectible']['Manufacture'],
						'license' => $collectible['Collectible']['License'],
						'collectibletype' => $collectible['Collectible']['Collectibletype']
					));?>
					<div class="links">

					</div>
					<div class="collectible actions">
						<?php echo $html -> link('Remove', array('controller' => 'collections', 'action' => 'remove', $collectible['id']));?>
						<?php echo $html -> link('Details', array('controller' => 'collections', 'action' => 'viewCollectible', $collectible['id']));?>
						<?php echo $html -> link('Edit', array('controller' => 'collections', 'action' => 'editCollectible', $collectible['id']));?>
					</div>
				</div>
				<?php endforeach;?>
				<div class="paging">
					<p>
						<?php
						echo $this -> Paginator -> counter( array('format' => __('Page %page% of %pages%, showing %current% collectibles out of %count% total.', true)));
						?>
					</p>
					<?php echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));?>
					<?php echo $this -> Paginator -> numbers();?>
					<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
				</div>
			</div>
		</div>
	</div>
</div>
