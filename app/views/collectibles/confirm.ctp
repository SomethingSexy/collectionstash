<div class="component" id="collectible-detail">
	<div class="inside">
		<div class="component-title">
			<h2>
			    <h2><?php  __('Confirm Collectible');?></h2>
			</h2>
		</div>
	    <?php echo $this->element('flash'); ?>
	    <div class="component-info">
	      	<div><?php __('You have submitted the following collectible to be added.  You will receive a confirmation e-mail once the collectible has been approved.  You will then be able to add it to your stash!');?></div> 
	    </div>
		<div class="component-view">
			<div class="collectibles">
				<div class="collectible item">
					<div class="collectible image">
						<?php echo $fileUpload -> image($collectible['Upload'][0]['name'], array('width' => '100'));?>
						<div class="collectible image-fullsize hidden">
							<?php echo $fileUpload -> image($collectible['Upload'][0]['name'], array('width' => 0));?>
						</div>
					</div>
					<div class="collectible detail">
						<dl>
							<dt>
								<?php __('Name');?>
							</dt>
							<dd>
								<?php echo $collectible['Collectible']['name'];?><?php
								if($collectible['Collectible']['exclusive']) { __(' - Exclusive');
								}
 								?>
							</dd>
							<?php
							if($collectible['Collectible']['variant']) {
								echo '<dt>';
								__('Variant:');
								echo '</dt><dd>';
								__('Yes');
								echo '</dd>';
							}
							?>
							<dt>
								<?php __('Manufacture');?>
							</dt>
							<dd>
								<a target="_blank" href="<?php echo $collectible['Manufacture']['url'];?>">
								<?php echo $collectible['Manufacture']['title'];?>
								</a>
							</dd>
							<dt>
								<?php __('Type');?>
							</dt>
							<dd>
								<?php echo $collectible['Collectibletype']['name'];?>
							</dd>
							<dt>
								<?php __('License');?>
							</dt>
							<dd>
								<?php echo $collectible['License']['name'];?>
							</dd>
							<dt>
								<?php __('Description');?>
							</dt>
							<dd>
								<?php echo $collectible['Collectible']['description'];?>
							</dd>
							<?php if(!empty($collectible['Collectible']['code'])){ ?>
							<dt>
								<?php __('Product Id');?>
							</dt>
							<dd>
								<?php echo $collectible['Collectible']['code'];?>
							</dd>
							<?php }?>
							<dt>
								<?php __('Original Retail Price');?>
							</dt>
							<dd>
								<?php echo $collectible['Collectible']['msrp'];?>
							</dd>
							<?php
								//$editionSize = $collectible['Collectible']['edition_size'];
								//if($collectible['Collectible']['showUserEditionSize'])
								//{ ?>

							<dt>
								<?php __('Edition Size');?>
							</dt>
							<dd>
								<?php echo $collectible['Collectible']['edition_size'];?>
							</dd>
							<?php //}?>
							<?php if(!empty($collectible['Collectible']['product_weight'])) {
								echo '<dt>';
								echo __('Weight');
								echo '</dt>';
								echo '<dd>';	
								echo $collectible['Collectible']['product_weight'];
								echo '</dd>';
							}?>
							<?php if(!empty($collectible['Collectible']['product_length'])) {
								echo '<dt>';
								echo __('Length');
								echo '</dt>';
								echo '<dd>';	
								echo $collectible['Collectible']['product_length'];
								echo '</dd>';
							}?>
							<?php if(!empty($collectible['Collectible']['product_width'])) {
								echo '<dt>';
								echo __('Width');
								echo '</dt>';
								echo '<dd>';	
								echo $collectible['Collectible']['product_width'];
								echo '</dd>';
							}?>
							
							<?php if(!empty($collectible['Collectible']['product_depth'])) {
								echo '<dt>';
								echo __('Depth');
								echo '</dt>';
								echo '<dd>';	
								echo $collectible['Collectible']['product_depth'];
								echo '</dd>';
							}?>
						</dl>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

