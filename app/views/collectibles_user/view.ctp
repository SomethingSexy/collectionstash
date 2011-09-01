<div class="component" id="collectible-detail">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php __('Your Collectible Details');?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>		
		<div class="component-view">
				<div class="collectible links">
					<?php echo $html -> link('Who has it?', array('controller' => 'collections', 'action' => 'who', $collectible['Collectible']['id']));?>
				</div>
				<div class="collectible item">
					<div class="collectible image">
						<?php 
						if (!empty($collectible['Collectible']['Upload'])) { ?>
							<?php echo $fileUpload -> image($collectible['Collectible']['Upload'][0]['name'], array('width' => '100'));?>
							<div class="collectible image-fullsize hidden">
								<?php echo $fileUpload -> image($collectible['Collectible']['Upload'][0]['name'], array('width' => 0));?>
							</div>						
							
						<?php } else { ?>
							<img src="/img/silhouette_thumb.png"/>
						<?php } ?>
						
						

					</div>
					<div class="collectible detail">
						<dl>
							<dt>
								<?php __('Date Added');?>
							</dt>
							<dd>
								<?php echo $collectible['CollectiblesUser']['created'];?>
							</dd>
							<dt>
								<?php __('Manufacture');?>
							</dt>
							<dd>
								<a target="_blank" href="<?php echo $collectible['Collectible']['Manufacture']['url'];?>">
								<?php echo $collectible['Collectible']['Manufacture']['title'];?>
								</a>
							</dd>
							<dt>
								<?php __('License');?>
							</dt>
							<dd>
								<?php echo $collectible['Collectible']['License']['name'];?>
							</dd>
							<dt>
								<?php __('Type');?>
							</dt>
							<dd>
								<?php echo $collectible['Collectible']['Collectibletype']['name'];?>
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
								<?php echo $collectible['Collectible']['Scale']['scale'];?>
							</dd>
							<?php if(!empty($collectibleCore['Collectible']['release']) && $collectibleCore['Collectible']['release'] !== '0000'){ ?>
								<dt>
									<?php __('Release Year');?>
								</dt>
								<dd>
									<?php echo $collectibleCore['Collectible']['release'];?>
								</dd>
								<?php }?>
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
							<?php
								$editionSize = $collectible['Collectible']['edition_size'];
								if($collectible['Collectible']['showUserEditionSize'])
								{ ?>

							<dt>
								<?php __('Edition Size');?>
							</dt>
							<dd><?php echo $collectible['CollectiblesUser']['edition_size'] . '/' . $collectible['Collectible']['edition_size']; ?></dd>  
							<?php }?>
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
		</div>
	</div>
</div>
