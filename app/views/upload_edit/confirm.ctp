<?php echo $this -> Html -> script('collectible-add', array('inline' => false));?>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php __('Edit Image');?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				<?php echo __('Thank you for updating the collectible image.  The update to the image will go live as soon as it is approved.  Click <a href="/collectibles/view/'.$collectibleId.'">here</a> to return to the collectible you are editing.');?>
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
				<?php } ?>
			</div>
		</div>
	</div>
</div>