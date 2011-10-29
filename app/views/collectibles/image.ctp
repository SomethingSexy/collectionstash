<?php echo $this -> Html -> script('collectible-add', array('inline' => false));?>
<?php if($this -> Session -> check('add.collectible.mode.variant')) { ?>
<?php      
	echo $this->element('collectible_detail', array(
		'title' => __('Base Collectible Details', true),
		'showStatistics' => false,
		'showWho' => false,
		'collectibleDetail' => $collectible
	));
?>
<?php } ?>
<div id="bread-crumbs">
	<?php echo $this->Wizard->progressMenu(array('manufacture'=>'Manufacturer Details', 'variantFeatures'=>'Variant Features', 'attributes'=>'Accessories/Features','tags'=>'Tags', 'image'=>'Image', 'review'=> 'Review')); ?>	
</div>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php __('Add Image');?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				<?php 
					if (isset($collectible['Upload'])) { 
						echo '<p>'.__('The following image has been added for this collectible.  You can change the image if you want or select continue to keep this image.').'</p>';
					} else {
						echo '<p>'.__('To add an image to the collectible, either select an image from your computer or enter in a URL that contains the image you wish to attach to this collectible.').'</p>';	
					}		
					?>
					<p><?php echo __('Image requirements:');?></p>
					<ul>
						<li><?php echo __('The image must be less than 2MB.');?></li>
					</ul>
					<p><?php echo __('Image recommendations:');?></p>
					<ul>
						<li><?php echo __('The image should be at least 150 x 150 pixels.');?></li>
						<li><?php echo __('This will be used as the default image for this collectible.  Thumbnails will look best if this image\'s height is bigger than it\'s width.');?></li>
						<li><?php echo __('Please try and use a professionally shot photo.');?></li>
					</ul>
			</div>
		</div>
		<div class="component-view add-image">
			<div class="collectible add">
				<?php 
					if (isset($this->data['Upload'])) { ?>
						<div class="collectible image">
							<?php echo $fileUpload -> image($this->data['Upload']['name'], array('width' => '0'));?>
							<div class="collectible image-fullsize hidden">
								<?php echo $fileUpload -> image($this->data['Upload']['name'], array('width' => 0));?>
							</div>								
						</div>	
						<div class="links">
							<?php echo $this -> Form -> create('Collectible', array('url' => '/'.$this->params['controller']. '/'.$this->action.'/image','id'=>'skip-image-form'));?>
								<input type="hidden" name="data[skip]" value="true" />
							</form>
							<?php 
								echo $this -> Form -> create('Collectible', array('url' => '/'.$this->params['controller']. '/'.$this->action.'/image' , 'type' => 'file', 'id' => 'remove-image-form'));
								echo '<input type="hidden" name="data[remove]" value="true" />';
								echo $this -> Form -> end(); 
							?>
							<a class="link" id="remove-image-submit">Change Image</a> <a class="link" id="skip-image-button">Continue</a>
						</div>
				<?php } else { ?>
					<?php echo $this -> Form -> create('Collectible', array('url' => '/'.$this->params['controller']. '/'.$this->action.'/image' , 'id'=>'add-image-form', 'type' => 'file'));?>
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
						<?php echo '<input type="hidden" name="data[remove]" value="false" />'; ?>		 
					</fieldset>
					</form>
					<?php echo $this -> Form -> create('Collectible', array('url' => '/'.$this->params['controller']. '/'.$this->action.'/image' ,'id'=>'skip-image-form'));?>
						<input type="hidden" name="data[skip]" value="true" />
					</form>
					<div class="links no-image">
						<input type="button" id="add-image-button" class="button" value="Add Image">
						<input type="button" id="skip-image-button" class="button" value="Skip">
					</div>
					
				<?php } ?>
				<script>
					//Eh move this out of here
					$('#add-image-button').click(function(){
						$('#add-image-form').submit();	
					});	
					$('#skip-image-button').click(function(){
						$('#skip-image-form').submit();	
					});	
				</script>
			</div>
		</div>
	</div>
</div>