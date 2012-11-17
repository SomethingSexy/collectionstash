<?php echo $this -> Minify -> script('js/tags', array('inline' => false));?>
<div id="bread-crumbs">
	<?php echo $this -> Wizard -> progressMenu(array('manufacture' => 'Manufacturer Details', 'attributes' => 'Accessories/Features', 'tags'=> 'Tags', 'image' => 'Image', 'review' => 'Review'));?>
</div>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Submit New Collectible', true);?></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<?php 
			if(isset($errors)) {
				echo $this -> element('errors', array('errors' => $errors));	
			}
		?>
		<div class="component-info">
			<div>
				<?php echo __('Tags are a great way to organize and categorize collectibles outside of the main manufacturer details.  Tags can give you and the community an easier way to find this collectible and similar ones.  You can add up to 5 tags for each collectible.  The input box below will help you find existing tags to add to this collectible.  Please try to use existing tags when possible.')
				?>
			</div>
		</div>
		<div class="component-view">
			<div class="collectible add add-tag">
				<?php echo $this -> Form -> create('Tag', array('url' => '/' . $this -> params['controller'] . '/' . $this -> action . '/tags', ));?>
				<fieldset>
					<legend>
						<?php echo __('Tags')
						?>
					</legend>
					<ul class="form-fields unstyled">
						<li>
							<input type="text" maxlength="25" class="text-box" name="q" id="query" />
							<button class="btn" id="add-query"><?php echo __('Add');?></button>
							<?php //echo $this -> Form -> input('tag_id', array('div' => false, 'label' => false));?>
						</li>
					</ul>
				</fieldset>
				<ul id="add-tag-list" class="tag-list no-link">
					<?php
					$lastKey = 0;
					if (isset($this -> data['Tag'])) {
						foreach ($this->data['Tag'] as $key => $tag) {
							echo '<li class="tag remove"><span class="tag-name">';
							echo $tag['Tag']['tag'];
							echo '</span><input type="hidden" name="data[CollectiblesTag][' . $key . '][tag]" value="' . $tag['Tag']['tag'] . '"/>';
							echo '<a class="ui-icon ui-icon-close remove-tag" data-type="l"></a>';
							echo '</li>';
							$lastKey = $key;
						}
						echo '<script>var lastTagKey =' . $lastKey . ';</script>';
					}
					?>
				</ul>
				<input type="submit" class="btn btn-primary" value="Submit"/>
				<?php echo $this -> Form -> end();?>
			</div>
		</div>
	</div>
</div>