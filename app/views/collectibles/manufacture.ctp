<?php echo $this -> Html -> script('collectible-add', array('inline' => false));?>
<div id="bread-crumbs">
	<?php echo $this->Wizard->progressMenu(array('manufacture'=>'Manufacturer Details', 'variantFeatures'=>'Variant Features', 'attributes'=>'Accessories/Features', 'tags'=>'Tags', 'image'=>'Image', 'review'=> 'Review')); ?>	
</div>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php echo __('Add New Collectible', true); ?>
			</h2>
			<?php if($this -> Session -> check('add.collectible.variant')) { ?>
			<div class="actions">
				<ul>
					<li>
						<a id="base-collectible-link" class="link"><?php echo __('View Base Collectible'); ?></a>
					</li>
				</ul>
			</div>
			<?php } ?>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				Fill out the following information to add the collectible.
			</div>
		</div>
		<div class="component-view">
			<div class="collectible add">
				<?php echo $this -> Form -> create('Collectible', array('url' => '/'.$this->params['controller']. '/'.$this->action.'/manufacture', 'type' => 'file')); ?>
				<fieldset>
					<legend><?php __('Manufacturer Details');?></legend>
					<ul class="form-fields">
						<li>
							<div class="label-wrapper">
								<label for="">
									<?php __('Manufacture') ?>
								</label>
							</div>	
							<div class="static-field">
								<?php echo $manufacturer['Manufacture']['title']; ?>	
								<?php echo '<input type="hidden" id="CollectibleManufactureId" value="'.$manufacturer['Manufacture']['id'].'" />'; ?>
							</div>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="">
									<?php __('Collectible Type') ?>
								</label>
							</div>	
							<div class="static-field">
								<?php echo $collectibleType['Collectibletype']['name'] ?>	
							</div>
						</li>
						</li>
						<?php
							if(!isset($specializedTypes)){
								$specializedTypes = array();	
							}
							if(empty($specializedTypes)) {
								echo '<li class="hidden">';
							} else {
								echo '<li>';
							}
						?>
						<div class="label-wrapper">
							<label for="">
								<?php __('Manufacturer Collectible Type') ?>
							</label>
						</div>
						<?php  
						if(!$this -> Session -> check('add.collectible.variant')) {
							echo $this -> Form -> input('specialized_type_id', array('empty' => true, 'div' => false, 'label' => false));
							
						} else {
							echo '<div class="static-field">';
							echo $collectible['SpecializedType']['name'];
							echo $this -> Form -> hidden('specialized_type_id');
							echo '</div>';							
						}?>
						</li>	
						<li>
							<div class="label-wrapper">
								<label for="CollectibleName">
									<?php __('Name') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('name', array('escape' => false,'div' => false, 'label' => false));?>
						</li>
						<?php
							if(empty($licenses)) {
								echo '<li class="hidden">';
							} else {
								echo '<li>';
							}
						?>
						<div class="label-wrapper">
							<label for="">
								<?php __('Brand/License') ?>
							</label>
						</div>
						<?php  
						if(!$this -> Session -> check('add.collectible.variant')) {
							echo $this -> Form -> input('license_id', array('div' => false, 'label' => false));
							
						} else {
							echo '<div class="static-field">';
							echo $collectible['License']['name'];
							echo $this -> Form -> hidden('license_id');
							echo '</div>';							
						}?>
						</li>
						<?php
							if(empty($series)) {
								echo '<li class="hidden">';
							} else {
								echo '<li>';
							}
						?>
						<div class="label-wrapper">
							<label for="">
								<?php __('Series') ?>
							</label>
						</div>
						<?php  
						if(!$this -> Session -> check('add.collectible.variant')) {
							echo $this -> Form -> input('series_id', array('empty' => true, 'div' => false, 'label' => false));
							
						} else {
							echo '<div class="static-field">';
							echo $collectible['Series']['name'];
							echo $this -> Form -> hidden('series_id');
							echo '</div>';							
						}?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="scale">
									<?php __('Scale') ?>
								</label>
							</div>
							<?php  
							if(!$this -> Session -> check('add.collectible.variant')) {
								echo $this -> Form -> input('scale_id', array('div' => false, 'label' => false));
								
							} else {
								echo '<div class="static-field">';
								echo $collectible['Scale']['scale'];
								echo $this -> Form -> hidden('scale_id');
								echo '</div>';							
							}?>
						</li>	
						<li>
							<div class="label-wrapper">
								<label for="CollectibleDescription">
									<?php __('Description') ?>
								</label>
							</div>
							<?php 
								$data = str_replace('\n', "\n", $this->data['Collectible']['description']);
        						$data = str_replace('\r', "\r", $data);
							
								echo $this -> Form -> input('description', array('escape' => false, 'div' => false, 'label' => false, 'value'=> $data));	
							?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleMsrp">
									<?php __('Original Retail Price (USD)') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('msrp', array('div' => false, 'label' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="scale">
									<?php __('Release Year') ?>
								</label>
							</div>
							<?php 
							    $current_year = date('Y');
    							$max_year = $current_year + 2;
								echo $this -> Form -> year('release', 1900, $max_year, true);?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleUrl">
									<?php __('URL') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('url', array('escape' => false, 'div' => false, 'label' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleEditionSize">
									<?php __('Limited Edition?') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('limited', array('div' => false, 'label' => false));?>
						</li>
						<?php if($this->data['Collectible']['limited']) { 
							echo '<li>';
						} else { 
							echo '<li class="hidden">';
						 } ?>
							<div class="label-wrapper">
								<label for="CollectibleEditionSize">
									<?php __('Edition Size') ?>
								</label>
								<a class="ui-icon ui-icon-info" title="<?php echo __('This is the edition size of the collectible.  If it is unknown or it does not have a specific edition size, leave blank.', true) ?>" alt="info"></a>
							</div>
							<?php echo $this -> Form -> input('edition_size', array('div' => false, 'label' => false));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleExclusive">
									<?php __('Exclusive') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('exclusive', array('div' => false, 'label' => false));?>
						</li>	
						<li>
							<div class="label-wrapper">
								<label for="">
									<?php __('Exclusive Retailer') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('retailer_id', array('div' => false, 'label' => false, 'empty' => true));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleCode">
									<?php __('Product Code') ?>
								</label>
								<a class="ui-icon ui-icon-info" title="<?php echo __('This is the item code or product code given by the manufacture.', true) ?>" alt="info"></a>
							</div>
							<?php echo $this -> Form -> input('code', array('div' => false, 'label' => false, 'between' => ''));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleUpc">
									<?php __('Product UPC') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('upc', array('div' => false, 'label' => false));?>
						</li>
						
						<li>
							<div class="label-wrapper">
								<label for="CollectibleProductWeight">
									<?php __('Weight (lbs)') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('product_weight', array('div' => false, 'label' => false));?>
						</li>
						<!--<div class="dimensions"><?php echo $this -> Form -> input('product_length', array('div' => false, 'size'=> '10', 'label' => false, 'id' => 'collectibleHeight'));?><span> x </span><?php echo $this -> Form -> input('product_width', array('div' => false, 'label' => false, 'id' => 'collectibleWidth'));?><span> x </span><?php echo $this -> Form -> input('product_depth', array('div' => false, 'label' => false, 'id' => 'collectibleDepth'));?></div>-->
						
						<li>
							<div class="label-wrapper">
								<label for="collectibleHeight">
									<?php __('Height (inches)') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('product_length', array('div' => false, 'label' => false, 'id' => 'collectibleHeight'));?>
						</li>
						<li id="widthWrapper">
							<div class="label-wrapper">
								<label for="collectibleWidth">
									<?php __('Width (inches)') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('product_width', array('div' => false, 'label' => false, 'id' => 'collectibleWidth'));?>
						</li>
						<li id="depthWrapper">
							<div class="label-wrapper">
								<label for="collectibleDepth">
									<?php __('Depth (inches)') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('product_depth', array('div' => false, 'label' => false, 'id' => 'collectibleDepth'));?>
						</li>						
					</ul>
				</fieldset>
				<?php echo $this -> Form -> end(__('Submit', true));?>
			</div>
		</div>
	</div>
</div>
<?php if($this -> Session -> check('add.collectible.variant')) { ?>
<div id="base-collectible" class="dialog">
	<?php      
		echo $this->element('collectible_detail_core', array(
			'showEdit' => false,
			'showImage' => false,
			'showAttributes' => false,
			'collectibleCore' => $collectible
		));
	?>
</div>
<?php } ?>
<script>
	$(function(){
		$(".ui-icon-info").tooltip({
			position: 'center right',
			opacity: 0.7
		});
		$('#base-collectible').dialog({
			'autoOpen' : false,
			'width' : 700,
			'height': 700,
			'resizable': false,
			'modal': true
		});	
		$('#base-collectible-link').click(function(){
			$('#base-collectible').dialog('open');
		});		
		
	});

</script>