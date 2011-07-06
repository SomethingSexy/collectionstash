<div class="collectible item">
	<div class="collectible image">
		<?php 
			if (!empty($collectibleCore['Upload'])) { ?>
				<?php echo $fileUpload -> image($collectibleCore['Upload'][0]['name'], array('width' => '100'));?>
				<div class="collectible image-fullsize hidden">
					<?php echo $fileUpload -> image($collectibleCore['Upload'][0]['name'], array('width' => 0));?>
				</div>
			<?php } else { ?>
				<img src="/img/silhouette_thumb.gif"/>
		<?php } ?>
		<?php if(isset($showEdit) && $showEdit) {
			echo '<div class="image link">';
			if (!empty($collectibleCore['Upload'])) { 
				echo '<a href="'.$editImageUrl.$collectibleCore['Collectible']['id'].'/'.$collectibleCore['Upload'][0]['id'].'">'.__('Edit', true).'</a>';
			} else {
				echo '<a href="'.$editImageUrl.$collectibleCore['Collectible']['id'].'/'.'">'.__('Edit', true).'</a>';
			}
			echo '</div>';			
		} ?>

	</div>
	<div class="collectible detail-wrapper">
		<div class="collectible detail">
			<div class="detail title">
				<h3><?php __('Manufacture Details'); ?></h3>
				<?php if(isset($showEdit) && $showEdit) {
					echo '<div class="title link">';
					echo '<a href="/collectibles/wizard/manufacture">Edit</a>';
					echo '</div>';			
				} ?>
			</div>

			<dl>
				<dt>
					<?php __('Manufacture');?>
				</dt>
				<dd>
					<a target="_blank" href="<?php echo $collectibleCore['Manufacture']['url'];?>">
					<?php echo $collectibleCore['Manufacture']['title'];?>
					</a>
				</dd>
				<dt>
					<?php __('License');?>
				</dt>
				<dd>
					<?php echo $collectibleCore['License']['name'];?>
				</dd>
				<dt>
					<?php __('Type');?>
				</dt>
				<dd>
					<?php echo $collectibleCore['Collectibletype']['name'];?>
				</dd>
				<dt>
					<?php __('Name');?>
				</dt>
				<dd>
					<?php echo $collectibleCore['Collectible']['name'];?><?php
					if(isset($collectibleCore['Collectible']['exclusive']) && $collectibleCore['Collectible']['exclusive']) { __(' - Exclusive');}
					?>
				</dd>
				<?php
				if(isset($collectibleCore['Collectible']['variant']) && $collectibleCore['Collectible']['variant']) {
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
					<?php echo $collectibleCore['Scale']['scale'];?>
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
					$data = str_replace('\n', "\n", $collectibleCore['Collectible']['description']);
					$data = str_replace('\r', "\r", $data);
	
					echo nl2br($data);
					//echo nl2br(htmlspecialchars($collectible['Collectible']['description'],ENT_QUOTES,'UTF-8',false));
					?>
				</dd>
				<?php if(!empty($collectibleCore['Collectible']['code'])){ ?>
				<dt>
					<?php __('Product code');?>
				</dt>
				<dd>
					<?php echo $collectibleCore['Collectible']['code'];?>
				</dd>
				<?php }?>
				<?php if(!empty($collectibleCore['Collectible']['upc'])){ ?>
				<dt>
					<?php __('Product UPC');?>
				</dt>
				<dd>
					<?php echo $collectibleCore['Collectible']['upc'];?>
				</dd>
				<?php }?>
				<dt>
					<?php __('Original Retail Price');?>
				</dt>
				<dd>
					<?php 
						if(strstr($collectibleCore['Collectible']['msrp'], '$') === false) {
							echo '$';
						}
						echo $collectibleCore['Collectible']['msrp'];?>
				</dd>
				<?php
				//$editionSize = $collectible['Collectible']['edition_size'];
				//if($collectible['Collectible']['showUserEditionSize'])
				//{
				?>
				<dt>
					<?php __('Limited Edition');?>
				</dt>
				<dd>
					<?php
					if($collectibleCore['Collectible']['limited']) {
						echo 'Yes';
					} else {
						echo 'No';
					}
					?>
				</dd>
				<?php
					if(!empty($collectibleCore['Retailer']['name'])) {
						echo '<dt>';
						echo  __('Exclusive Retailer');
						echo '</dt>';
						echo '<dd>';
						echo $collectibleCore['Retailer']['name'];
						echo '</dd>';
					}
				?>				
				<?php
					if(!empty($collectibleCore['Collectible']['edition_size'])) {
						echo '<dt>';
						echo  __('Edition Size');
						echo '</dt>';
						echo '<dd>';
						echo $collectibleCore['Collectible']['edition_size'];
						echo '</dd>';
					}
				?>				
				<?php
					if(!empty($collectibleCore['Collectible']['product_weight'])) {
						echo '<dt>';
						echo __('Weight');
						echo '</dt>';
						echo '<dd>';
						echo $collectibleCore['Collectible']['product_weight'] . ' lbs';
						echo '</dd>';
					}
				?>
				<?php
					if(!empty($collectibleCore['Collectible']['product_length'])) {
						echo '<dt>';
						echo __('Length');
						echo '</dt>';
						echo '<dd>';
						echo $collectibleCore['Collectible']['product_length'] . '"';
						echo '</dd>';
					}
				?>
				<?php
					if(!empty($collectibleCore['Collectible']['product_width'])) {
						echo '<dt>';
						echo __('Width');
						echo '</dt>';
						echo '<dd>';
						echo $collectibleCore['Collectible']['product_width'] . '"';
						echo '</dd>';
					}
				?>
				
				<?php
					if(!empty($collectibleCore['Collectible']['product_depth'])) {
						echo '<dt>';
						echo __('Depth');
						echo '</dt>';
						echo '<dd>';
						echo $collectibleCore['Collectible']['product_depth'] . '"';
						echo '</dd>';
					}
				?>
			</dl>
		</div>
		<div class="collectible detail">
			<div class="detail title">
				<h3><?php __('Accessories/Features'); ?></h3>
				<?php if(isset($showEdit) && $showEdit) {
					echo '<div class="title link">';
					echo '<a href="/collectibles/wizard/attributes">Edit</a>';
					echo '</div>';			
				} ?>
			</div>
			<div class="attributes-list">
				<ul>
					<?php
					$lastKey = 0;
					if(!empty($collectibleCore['AttributesCollectible'])) {
						echo '<li class="title">';
						echo '<span class="attribute-name">'.__('Part', true).'</span>';
						echo '<span class="attribute-description">'.__('Description', true).'</span>';
						echo '</li>';
							
						foreach($collectibleCore['AttributesCollectible'] as $key => $attribute) {
							if($attribute['variant'] !== '1') {
								echo '<li>';
								echo '<span class="attribute-name">';
								echo $attribute['Attribute']['name'];
								echo '</span>';
								echo '<span class="attribute-description">';
								echo $attribute['description'];
								echo '</span>';
								echo '</li>';
							}
						}
					}
					?>
				</ul>
			</div>
		
		</div>
		<?php if(isset($collectibleCore['Collectible']['variant']) && $collectibleCore['Collectible']['variant']) { ?>
		<div class="collectible detail">
			<div class="detail title">
				<h3><?php __('Variant Accessories/Features'); ?></h3>
				<?php if(isset($showEdit) && $showEdit) {
					echo '<div class="title link">';
					echo '<a href="/collectibles/wizard/attributes">Edit</a>';
					echo '</div>';			
				} ?>
			</div>
			<div class="attributes-list">
				<ul>
					<?php
					$lastKey = 0;
					if(!empty($collectibleCore['AttributesCollectible'])) {
						echo '<li class="title">';
						echo '<span class="attribute-name">'.__('Part', true).'</span>';
						echo '<span class="attribute-description">'.__('Description', true).'</span>';
						echo '</li>';
							
						foreach($collectibleCore['AttributesCollectible'] as $key => $attribute) {
							if($attribute['variant'] === '1') {
								echo '<li>';
								echo '<span class="attribute-name">';
								echo $attribute['Attribute']['name'];
								echo '</span>';
								echo '<span class="attribute-description">';
								echo $attribute['description'];
								echo '</span>';
								echo '</li>';
							}
						}
					}
					?>
				</ul>
			</div>
		
		</div>
		<?php } ?>
			<?php
			if(isset($showStatistics) && $showStatistics) { ?>
			<div class="collectible detail statistics">
				<div class="detail title">
					<h3><?php __('Collectible Statistics');?></h3>
				</div>
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