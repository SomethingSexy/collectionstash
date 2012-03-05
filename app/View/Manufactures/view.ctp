<div class="component" id="manufacturer-profile">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $manufacture['Manufacture']['title']
			?></h2>
			<span class="url"><?php echo $manufacture['Manufacture']['url']
				?></span>
		</div>
		<div class="component-view">
			<div id="tabs">
				<ul>
					<li>
						<a href="#tabs-1"><?php echo __('Stats');?></a>
					</li>
					<li>
						<a href="#tabs-2"><?php echo __('Brands');?></a>
					</li>
					<li>
						<a href="#tabs-3"><?php echo __('Collectible Types');?></a>
					</li>
				</ul>
				<div id="tabs-1">
					<div class="stats">
						<dl>
				
							<dt><?php echo __('Total Collectibles');?></dt><dd><?php echo $manufacture['Manufacture']['collectible_count']
								?></dd>
				
							<dt><?php echo __('Percentage of Total Collectibles');?></dt><dd><?php echo $manufacture['Manufacture']['percentage_of_total']
								?></dd>
					
							<dt><?php echo __('Total Collectible Types');?></dt><dd><?php echo $manufacture['Manufacture']['collectibletype_count']
								?></dd>
					
							<dt><?php echo __('Total Brands');?></dt><dd><?php echo $manufacture['Manufacture']['license_count']
								?></dd>
							<dt><?php echo __('Highest Price');?></dt><dd><?php echo $manufacture['Manufacture']['highest_price']
								?></dd>
							<dt><?php echo __('Lowest Price');?></dt><dd><?php echo $manufacture['Manufacture']['lowest_price']
								?></dd>	
							<?php 
								if(isset($manufacture['Manufacture']['highest_edition_size'])) {
									echo '<dt>';
									echo __('Highest Edition Size');
									echo '</dt>';
									echo '<dd>';
									echo $manufacture['Manufacture']['highest_edition_size'];
									echo '</dd>';		
								}					
							?>							
							<?php 
								if(isset($manufacture['Manufacture']['lowest_edition_size'])) {
									echo '<dt>';
									echo __('Lowest Edition Size');
									echo '</dt>';
									echo '<dd>';
									echo $manufacture['Manufacture']['lowest_edition_size'];
									echo '</dd>';		
								}					
							?>							
						</dl>
					</div>
				</div>
				<div id="tabs-2">
					<div class="licenses">
						<h3><?php echo __('Licenses');?></h3>
						<ul class="tag-list">
							<?php
							foreach ($licenses as $key => $value) {
								echo '<li class="tag"><span class="tag-name">';
								echo '<a href="/collectibles/search?m='.$manufacture['Manufacture']['id'].'&l='.$key.'">'.$value.'</a>';
								echo '</span></li>';
							}
							?>
						</ul>
					</div>
				</div>
				<div id="tabs-3">

				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(function() {
		$("#tabs").tabs();
	});

</script>