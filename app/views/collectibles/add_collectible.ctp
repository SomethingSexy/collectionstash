<?php echo $this -> Html -> script('collectible-add', array('inline' => false));?>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php __('Add Collectible');?>
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
				<?php echo $this -> Form -> create('Collectible', array('type' => 'file'));?>
				<fieldset>
					<legend><?php __('Details');?></legend>
					<ul class="form-fields">
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
							<?php echo $this -> Form -> input('description', array('div' => false, 'label' => false, 'escape' => false));?>
						</li>
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
								<label for="CollectibleMsrp">
									<?php __('Original Retail Price (USD)') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('msrp', array('div' => false, 'label' => false));?>
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
						<li>
							<div class="label-wrapper">
								<label for="CollectibleUrl">
									<?php __('URL') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('url', array('div' => false, 'label' => false));?>
						</li>
						
					</ul>
					<?php if(!isset($edit)) { ?>
						<input type="hidden" name="data[Edit]" value="false" />
					<?php } else {?>
						<input type="hidden" name="data[Edit]" value="true" />
					<?php } ?>
					<script>
						$(".ui-icon-info").tooltip({
							position: 'center right',
							opacity: 0.7
						});
					</script>
				</fieldset>
				<?php if(!isset($edit)) { ?>
					<fieldset>
						<legend><?php __('Image');?></legend>
						<ul class="form-fields">	
						
							<li>
								<div class="label-wrapper">
									<label for="Upload0File">
										<?php __('Upload image from your computer') ?>
									</label>
								</div>
								<?php echo $this -> Form -> input('Upload.0.file', array('div' => false, 'type' => 'file', 'label' => false));?>
							</li>
							<li>
								<div class="label-wrapper">
									<label for="Upload0File">
										<?php __('Upload image from URL') ?>
									</label>
								</div>
								<?php echo $this -> Form -> input('Upload.0.url', array('div' => false, 'label' => false));?>
							</li>
						
						</ul>
					</fieldset>
				<?php }?>
				<?php echo $this -> Form -> end(__('Submit', true));?>
			</div>
		</div>
	</div>
</div>