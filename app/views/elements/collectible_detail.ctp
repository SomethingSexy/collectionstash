<?php 
	if(isset($setPageTitle) && $setPageTitle) {
		$this->set("title_for_layout", $collectible['Manufacture']['title'].' - '.$collectible['Collectible']['name']);
	}
	$this->set('description_for_layout', $collectible['Manufacture']['title'].' '.$collectible['Collectible']['name']);
	$this->set('keywords_for_layout', $collectible['Manufacture']['title'].' '.$collectible['Collectible']['name'].','.$collectible['Collectible']['name'].','.$collectible['Collectibletype']['name'].','.$collectible['License']['name']);
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
					echo $html -> link('Who has it?', array('controller' => 'collections', 'action' => 'who', $collectible['Collectible']['id']));
				}
				if(isset($showEdit) && $showEdit) {
					echo $html -> link('Edit', array('controller'=>'collectibleEdit', 'action' => 'edit', $collectible['Collectible']['id']));
				}
				if(isset($showHistory) && $showHistory) {
					echo $html -> link('History', array( 'action' => 'history', $collectible['Collectible']['id']));
				}
				?>
			</div>
			<?php echo $this->element('collectible_detail_core');	?>	
			<?php
			if($showStatistics) { ?>
			<div class="collectible statistics">
				<h3>
				<?php __('Collectible Statistics');?>
				</h3>
				<dl>
					<dt>
						<?php __('Total owned: ');?>
					</dt>
					<dd>
						<?php echo $collectibleCount;?>
					</dd>
				</dl>
			</div>
			<?php }?>
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
<?php }} ?>