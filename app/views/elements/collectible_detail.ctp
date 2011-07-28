<?php 
	if(isset($setPageTitle) && $setPageTitle) {
		$this->set("title_for_layout", $collectibleDetail['Manufacture']['title'].' - '.$collectibleDetail['Collectible']['name']);
	}
	$this->set('description_for_layout', $collectibleDetail['Manufacture']['title'].' '.$collectibleDetail['Collectible']['name']);
	$this->set('keywords_for_layout', $collectibleDetail['Manufacture']['title'].' '.$collectibleDetail['Collectible']['name'].','.$collectibleDetail['Collectible']['name'].','.$collectibleDetail['Collectibletype']['name'].','.$collectibleDetail['License']['name']);
?>
<div class="component" id="collectible-detail">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php echo $title;?>
			</h2>
		</div>
		<div class="component-view">
			<div class="collectible links">
				<?php
				if($showWho) {
					echo $html -> link('Who has it?', array('controller' => 'collections', 'action' => 'who', $collectibleDetail['Collectible']['id']));
				}
				// if(isset($showEdit) && $showEdit) {
					// echo $html -> link('Edit', array('controller'=>'collectibleEdit', 'action' => 'edit', $collectible['Collectible']['id']));
				// }
				if(isset($showHistory) && $showHistory) {
					echo $html -> link('History', array( 'action' => 'history', $collectibleDetail['Collectible']['id']));
				}
				?>
			</div>
			<?php 
				if(!isset($showEdit)){
					$showEdit = false;
				}
				if(!isset($editImageUrl)){
					$editImageUrl = false;
				}
				if(!isset($editManufactureUrl)){
					$editManufactureUrl = '';
				}				
				echo $this->element('collectible_detail_core', array(
				'showEdit' => $showEdit,
				'editImageUrl'=> $editImageUrl,
				'editManufactureUrl' => $editManufactureUrl,
				'showStatistics' => $showStatistics,
				'collectibleCore' => $collectibleDetail
			));		?>	
			
		</div>
	</div>
</div>


<?php 
	if(isset($showVariants) && $showVariants) {
		if (!empty($variants)) { ?>
		<div class="component" id="collectibles-list-component">
		  <div class="inside" >
		     <div class="component-title">
		      <h2><?php __('Variants');?></h2>
		    </div>
		    <div class="component-view">
		      <div class="collectibles view">
		        <?php  
		        foreach ($variants as $variant):
		        ?>
		        	<div class="collectible item">
		            	<?php echo $this -> element('collectible_list_image', array(
							'collectible' => $variant
						));?>
						<?php echo $this -> element('collectible_list_detail', array(
							'collectible' => $variant['Collectible'],
							'manufacture' => $variant['Manufacture'],
							'license' => $variant['License'],
							'collectibletype' => $variant['Collectibletype']
						));?>
		        	 <div class="collectible actions"><?php echo $html->link('Details', array('controller' => 'collectibles', 'action' => 'view', $variant['Collectible']['id'])); ?></div>
		          </div>
		        <?php endforeach; ?>      
		      </div>
		    </div>
		  </div>
		</div>	
	<?php } else { ?>
		<div class="component" id="collectibles-list-component">
		  <div class="inside" >
		     <div class="component-title">
		      <h2><?php __('Variants');?></h2>
		    </div>
		    <div class="component-view">
		      <div class="collectibles view empty">
   				<p><?php __('This collectible has no variants.'); ?></p>	
		      </div>
		    </div>
		  </div>
		</div>		
	<?php	  }
} ?>