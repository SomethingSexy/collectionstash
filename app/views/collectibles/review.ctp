<div class="component" id="collectible-detail">
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
		<div class="component-view">
			<div class="collectibles">
				<div class="collectible item">
					<div class="collectible image">
						<?php 
							if (!empty($collectible['Upload'])) { ?>
								<?php echo $fileUpload -> image($collectible['Upload']['name'], array('width' => '100'));?>
								<div class="collectible image-fullsize hidden">
									<?php echo $fileUpload -> image($collectible['Upload']['name'], array('width' => 0));?>
								</div>								
								
							<?php } else { ?>
								<img src="/img/silhouette.gif"/>
							<?php } ?>
					</div>
					<div class="collectible detail">
						<dl>
							<dt>
								<?php __('Manufacture');?>
							</dt>
							<dd>
								<a target="_blank" href="<?php echo $collectible['Manufacture']['url'];?>">
								<?php echo $collectible['Manufacture']['title'];?>
								</a>
							</dd>
							<dt>
								<?php __('License');?>
							</dt>
							<dd>
								<?php echo $collectible['License']['name'];?>
							</dd>
							<dt>
								<?php __('Type');?>
							</dt>
							<dd>
								<?php echo $collectible['Collectibletype']['name'];?>
							</dd>

							<dt>
								<?php __('Name');?>
							</dt>
							<dd>
								<?php echo $collectible['Collectible']['name'];?><?php
								if(isset($collectible['Collectible']['exclusive']) && $collectible['Collectible']['exclusive']) { __(' - Exclusive');
								}
 								?>
							</dd>
							<?php
							if(isset($collectible['Collectible']['variant']) && $collectible['Collectible']['variant']) {
								echo '<dt>';
								__('Variant:');
								echo '</dt><dd>';
								__('Yes');
								echo '</dd>';
							}
							?>
							<dt>
								<?php __('Scale');?>
							</dt>
							<dd>
								<?php echo $collectible['Scale']['scale'];?>
							</dd>
							<?php if(!isset($collectible['Collectible']['release']['year'])) {
								echo '<dt>';
								echo __('Release Year');
								echo '</dt>';
								echo '<dd>';	
								echo $collectible['Collectible']['release']['year'];
								echo '</dd>';
							}?>							
							<dt>
								<?php __('Description');?>
							</dt>
							<dd>
								<?php 
									$data = str_replace('\n', "\n", $collectible['Collectible']['description']);
            						$data = str_replace('\r', "\r", $data);
									
									echo nl2br($data);
								//echo nl2br(htmlspecialchars($collectible['Collectible']['description'],ENT_QUOTES,'UTF-8',false)); ?>
							</dd>
							<?php if(!empty($collectible['Collectible']['code'])){ ?>
							<dt>
								<?php __('Product code');?>
							</dt>
							<dd>
								<?php echo $collectible['Collectible']['code'];?>
							</dd>
							<?php }?>
							<?php if(!empty($collectible['Collectible']['upc'])){ ?>
							<dt>
								<?php __('Product UPC');?>
							</dt>
							<dd>
								<?php echo $collectible['Collectible']['upc'];?>
							</dd>
							<?php }?>
							<dt>
								<?php __('Original Retail Price');?>
							</dt>
							<dd>
								<?php echo '$'.$collectible['Collectible']['msrp'];?>
							</dd>
							<?php
								//$editionSize = $collectible['Collectible']['edition_size'];
								//if($collectible['Collectible']['showUserEditionSize'])
								//{ ?>
							<dt>
								<?php __('Limited Edition');?>
							</dt>
							<dd>
								<?php if($collectible['Collectible']['limited']) {
									echo 'Yes';
								} else {
									echo 'No';
								} ?>
							</dd>
							<?php if(!empty($collectible['Collectible']['edition_size'])) {
								echo '<dt>';
								echo __('Edition Size');
								echo '</dt>';
								echo '<dd>';	
								echo $collectible['Collectible']['edition_size'];
								echo '</dd>';
							}?>
							<?php if(!empty($collectible['Collectible']['product_weight'])) {
								echo '<dt>';
								echo __('Weight');
								echo '</dt>';
								echo '<dd>';	
								echo $collectible['Collectible']['product_weight'].' lbs';
								echo '</dd>';
							}?>
							<?php if(!empty($collectible['Collectible']['product_length'])) {
								echo '<dt>';
								echo __('Length');
								echo '</dt>';
								echo '<dd>';	
								echo $collectible['Collectible']['product_length'].'"';
								echo '</dd>';
							}?>
							<?php if(!empty($collectible['Collectible']['product_width'])) {
								echo '<dt>';
								echo __('Width');
								echo '</dt>';
								echo '<dd>';	
								echo $collectible['Collectible']['product_width'].'"';
								echo '</dd>';
							}?>
							
							<?php if(!empty($collectible['Collectible']['product_depth'])) {
								echo '<dt>';
								echo __('Depth');
								echo '</dt>';
								echo '<dd>';	
								echo $collectible['Collectible']['product_depth'].'"';
								echo '</dd>';
							}?>
						</dl>
					</div>
				</div>
			</div>
			<?php echo $this->Form->create('Collectible' , array('url' => '/collectibles/confirm'));?>
			<?php echo $this->Form->end(__('Submit', true));?>
			<a href="<?php echo $referer ?>/edit:1">Edit</a>
		</div>
	</div>
</div>







