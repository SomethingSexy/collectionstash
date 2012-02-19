
<div class="component" id="collectibles-list-component">
	<div class="inside" >
		<div class="component-title">
			<h2><?php echo __('Contribute Variant - Select Collectible');?></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				<p>
					<?php echo __('To add a variant, you need to find the collectible this will be a variant of.  Browser through the list or use the search to narrow the results.  Click select to continue.');?>
				</p>
			</div>
		</div>
		<div class="component-view">
			<div class="collectibles view">
				<?php echo $this -> element('search_collectible', array("searchUrl" => '/collectibles/variantSelectCollectible'));?>
				<?php echo $this -> element('search_filters', array("searchUrl" => '/collectibles/variantSelectCollectible','lockManufacturer' => true));?>
				<?php
				foreach ($collectibles as $collectible):
				?>
				<div class="collectible item">
					<?php echo $this -> element('collectible_list_image', array('collectible' => $collectible));?>
					<?php echo $this -> element('collectible_list_detail', array('collectible' => $collectible['Collectible'], 'manufacture' => $collectible['Manufacture'], 'license' => $collectible['License'], 'collectibletype' => $collectible['Collectibletype'],'showStatus'=> true));?>
					<div class="links">
						<?php echo $this -> Html -> link('Select', array('action' => 'variantSelectCollectible', $collectible['Collectible']['id']));?>
					</div>
					<div class="collectible actions">
						<?php echo $this -> Html -> link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'], $collectible['Collectible']['slugField']));?>
					</div>
				</div>
				<?php endforeach;?>
				<div class="paging">
					<p>
						<?php
						echo $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
						?>
					</p>
					<?php
					$urlparams = $this -> request -> query;
					unset($urlparams['url']);
					$this -> Paginator -> options(array('url' => array('?' => http_build_query($urlparams))));
					echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));
					?>
					<?php echo $this -> Paginator -> numbers(array('separator'=> false));?>
					<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
				</div>
			</div>
		</div>
	</div>
</div>