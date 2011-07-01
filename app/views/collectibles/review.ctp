<div id="bread-crumbs">
	<?php echo $this->Wizard->progressMenu(); ?>	
</div>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			    <h2><?php  __('Review Collectible');?></h2>
			</h2>
		</div>
	    <?php echo $this->element('flash'); ?>
	    <div class="component-info">
	      	<div><?php __('Please review the collectible below.');?></div> 
	    </div>
		<div class="component-view review">
			<?php echo $this->element('collectible_detail_core');	?>		
			<div class="links review">
				<?php echo $this->Form->create('Collectible' , array('url' => $this->here));?>
					<input type="hidden" name="data[balls]" value="Submit">
					<input type="submit" value="Submit">
				</form>
				<?php 
					if($this -> Session -> check('add.collectible.mode.collectible')) {
						echo '<a href="/collectibles/addCollectible/edit:1">Edit</a>';	
						
					} else if($this -> Session -> check('add.collectible.mode.variant')) {
						echo '<a href="/collectibles/addVariant/edit:1">Edit</a>';
					} else if($this -> Session -> check('edit.collectible.mode.collectible')) {
						echo '<a href="/collectibles/edit/edit:1">Edit</a>';
					}
					?>
			</div>
		</div>
	</div>
</div>







