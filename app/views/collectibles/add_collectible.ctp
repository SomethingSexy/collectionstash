<?php echo $this -> Html -> script('collectible-add', array('inline' => false));?>
<?php if($this -> Session -> check('add.collectible.mode.variant')) { ?>
<?php echo $this -> Html -> script('variant-add', array('inline' => false));?>
<?php      
	echo $this->element('collectible_detail', array(
		'title' => __('Base Collectible Details', true),
		'showStatistics' => false,
		'showWho' => false
	));
?>
<?php } ?>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php echo $collectible_title ?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				Fill out the following information to add the collectible.
			</div>
		</div>
		<div class="component-view">
			<div class="collectible add">
				<?php 
					if(isset($collectible_action)) {
						echo $this -> Form -> create('Collectible', array('url'=> $collectible_action , 'type' => 'file'));
					} else {
						echo $this -> Form -> create('Collectible', array('url' => $this->here, 'type' => 'file'));
					}
					?>
				<fieldset>
					<legend><?php __('Details');?></legend>
					<ul class="form-fields">
						<?php  if($this -> Session -> check('add.collectible.mode.collectible') || $this -> Session -> check('edit.collectible.mode.collectible')) { ?>
							<li>
								<div class="label-wrapper">
									<label for="">
										<?php __('Manufacture') ?>
									</label>
								</div>
								<?php echo $this -> Form -> input('manufacture_id', array('div' => false, 'label' => false));?>
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
							<?php echo $this -> Form -> input('license_id', array('div' => false, 'label' => false));?>
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
									<?php __('Category') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('series_id', array('div' => false, 'label' => false));?>
							</li>
		
							<?php
								if(empty($collectibletypes)) {
									echo '<li class="hidden">';
								} else {
									echo '<li>';
								}
							?>
							<div class="label-wrapper">
								<label for="">
									<?php __('What type of collectible is this?') ?>
								</label>
							</div>
							<?php  echo $this -> Form -> select('Collectible.collectibletype_id', $collectibletypes, null, array('label' => false, 'empty' => false));?>
							</li>
							<li>
								<div class="label-wrapper">
									<label for="scale">
										<?php __('Scale') ?>
									</label>
								</div>
								<?php echo $this -> Form -> input('scale_id', array('div' => false, 'label' => false));?>
							</li>	
					    <?php } ?>
					    <li>
							<div class="label-wrapper">
								<label for="scale">
									<?php __('Release Year') ?>
								</label>
							</div>
							<?php echo $this -> Form -> year('release', 1900, date('Y'));?>
						</li>
						<li>
							<div class="label-wrapper">
								<label for="CollectibleName">
									<?php __('Name') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('name', array('div' => false, 'label' => false));?>
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
							
								echo $this -> Form -> input('description', array('div' => false, 'label' => false, 'value'=> $data));	
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
								<label for="CollectibleUrl">
									<?php __('URL') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('url', array('div' => false, 'label' => false));?>
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
					<?php if(!isset($edit)) { ?>
						<input type="hidden" name="data[Edit]" value="false" />
					<?php } else {?>
						<input type="hidden" name="data[Edit]" value="true" />
					<?php } ?>

				</fieldset>
				<fieldset>
					<legend>
						<?php __('Part Break Down');?>
					</legend>
					<ul class="form-fields">
						<li>
							<div class="label-wrapper">
								<label for="">
									<?php __('Features') ?>
								</label>
								<a class="ui-icon ui-icon-info" title="<?php echo __('Select add, to add an feature for this collectible.  An feature is a way to define what makes this collectible an exclusive or variant.', true) ?>" alt="info"></a>
							</div>
							<div id="collectible-attributes-list" class="attributes-list">
								<ul>
									<?php
									$lastKey = 0;
									if(isset($this -> data['AttributesCollectible'])) {
										foreach($this->data['AttributesCollectible'] as $key => $attribue) {
											if($attribue['variant'] !== '1') {
												echo '<li>';
												echo '<span class="attribute-label">Part: </span>';
												echo '<span class="attribute-name">';
												echo $attribue['name'];
												echo '</span>';
												echo '<span class="attribute-description">';
												echo $attribue['description'];
												echo '</span>';
												echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][attribute_id]" value="' . $attribue['attribute_id'] . '"/>';
												echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][description]" value="' . $attribue['description'] . '"/>';
												echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][name]" value="' . $attribue['name'] . '"/>';
												echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][variant]" value="' . $attribue['variant'] . '"/>';
												echo '</li>';
												$lastKey = $key;
											}
										}
										if(!$this -> Session -> check('add.collectible.mode.variant')) {
											echo '<script>var lastAttributeKey =' . $lastKey . ';</script>';
										}
									}
									?>
								</ul>
							</div>
							<div>
								<a class="ui-icon ui-icon-plus add-attribute">Add Attribute</a>
							</div>
						</li>
					</ul>
				</fieldset>	
				<?php if($this -> Session -> check('add.collectible.mode.variant')) { ?>
					<fieldset>
						<legend>
							<?php __('Variant Details');?>
						</legend>
						<ul class="form-fields">
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
						</ul>
					</fieldset>	
					<fieldset>
						<legend>
							<?php __('Variant Features');?>
						</legend>
						<ul class="form-fields">
							<li>
								<div id="variant-attributes-list" class="attributes-list">
									<ul>
										<?php
	
										if(isset($this -> data['AttributesCollectible'])) {
											foreach($this->data['AttributesCollectible'] as $key => $attribue) {
												if($attribue['variant'] === '1') {
													echo '<li>';
													echo '<span class="attribute-label">Feature: </span>';
													echo '<span class="attribute-name">';
													echo $attribue['name'];
													echo '</span>';
													echo '<span class="attribute-description">';
													echo $attribue['description'];
													echo '</span>';
													echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][attribute_id]" value="' . $attribue['attribute_id'] . '"/>';
													echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][description]" value="' . $attribue['description'] . '"/>';
													echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][name]" value="' . $attribue['name'] . '"/>';
													echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][variant]" value="' . $attribue['variant'] . '"/>';
													echo '</li>';
													$lastKey = $key;
												}
											}
											echo '<script>var lastAttributeKey =' . $lastKey . ';</script>';
										}
										?>
									</ul>
								</div>
								<div>
									<a class="add-variant-attribute">Add Feature</a>
								</div>
							</li>
						</ul>
					</fieldset>						
					
				<?php } ?>	
				<?php echo $this -> Form -> end(__('Submit', true));?>
			</div>
		</div>
	</div>
</div>
<script>
	$(".ui-icon-info").tooltip({
		position: 'center right',
		opacity: 0.7
	});
</script>

<?php if($this -> Session -> check('add.collectible.mode.variant')) { ?>
<div id="add-attribute-dialog" class="dialog" title="Add Attribute">
	<div class="component">
		<div class="inside" >
			<div class="component-info">
				<div>
					<?php __('Fill out the information below to add an Attribute to this variant.') ?>
				</div>
			</div>
			<div class="component-view">
				<fieldset>
					<ul id="add-attribute-dialog-fields" class="form-fields">
						<li id="description-field">
							<div class="label-wrapper">
								<label for="CollectibleName">
									<?php __('Description') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('description', array('maxlength' => 50, 'id' => 'attributeDescription', 'div' => false, 'label' => false));?>
						</li>
					</ul>
				</fieldset>
			</div>
		</div>
	</div>
</div>
<?php } ?>