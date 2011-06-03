<?php echo $this -> Html -> script('collectible-add', array('inline' => false));?>
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
						echo __('The following image has been added for this collectible.  You can change the image if you want or select continue to keep this image.');
					} else {
						echo __('To add an image to the collectible, either select an image from your computer or enter in a URL that contains the image you wish to attach to this collectible.');	
					}		
					?>
			</div>
		</div>
		<div class="component-view add-image">
			<div class="collectible add">
				<?php 
					if (isset($collectible['Upload'])) { ?>
						<div class="collectible image">
							<?php echo $fileUpload -> image($collectible['Upload']['name'], array('width' => '0'));?>
							<div class="collectible image-fullsize hidden">
								<?php echo $fileUpload -> image($collectible['Upload']['name'], array('width' => 0));?>
							</div>								
						</div>	
						<div class="links">
							<?php 
								echo $this -> Form -> create('Collectible', array('type' => 'file', 'id' => 'remove-image-form'));
								echo '<input type="hidden" name="data[remove]" value="true" />';
								echo $this -> Form -> end(); 
							?>
							<a id="remove-image-submit">Change Image</a> <a href="/collectibles/review">Continue</a>
						</div>
				<?php } else { ?>
					<?php echo $this -> Form -> create('Collectible', array('type' => 'file'));?>
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
					<div class="links no-image">
						<input type="submit" value="Submit">
						<a href="/collectibles/review">Skip</a>
					</div>
					</form>
				<?php } ?>
			</div>
		</div>
	</div>
</div>