<?php echo $this -> Html -> script('collectible-add', array('inline' => false));?>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
				<?php 
				if($addImage){
					echo __('Add Image');
				} else {
					echo __('Change Image');
				}
				
				?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				<?php 
					if (isset($collectible['Upload'])) { 
						echo __('The following image has been added for this collectible.  You can change the image if you want or select continue to keep this image.');
					} else {
						echo __('To add an image to the collectible, either select an image from your computer or enter in a URL that contains the image you wish to attach to this collectible.');	
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
					if (isset($collectible['Upload'])) { ?>
						<div class="collectible image">
							<?php echo $this -> FileUpload -> image($collectible['Upload']['name'], array('width' => '0'));?>
							<div class="collectible image-fullsize hidden">
								<?php echo $this -> FileUpload -> image($collectible['Upload']['name'], array('width' => 0));?>
							</div>								
						</div>	
				<?php } ?>
					<?php echo $this -> Form -> create('Collectible', array('url'=>'/upload_edits/edit', 'id'=>'add-image-form', 'type' => 'file'));?>
					<fieldset>
						<legend><?php 
						if($addImage){
							echo __('Add Image');
						} else {
							echo __('Change Image');
						}
						
						?></legend>
						<ul class="form-fields">	
						
							<li>
								<div class="label-wrapper">
									<label for="Upload0File">
										<?php echo __('Upload image from your computer') ?>
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
					<?php echo $this -> Form -> create('Collectible', array('url'=>'/collectibles/view/'.$collectibleId, 'id'=>'skip-image-form'));?>
						<input type="hidden" name="data[skip]" value="true" />
					</form>
					<div class="links no-image">
						<?php 
						if($addImage){
							echo '<input type="button" id="add-image-button" class="button" value="Add Image">';	
						} else {
							echo '<input type="button" id="add-image-button" class="button" value="Change Image">';
						}?>
						
						<input type="button" id="skip-image-button" class="button" value="Cancel">
					</div>
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