<?php echo $this -> Html -> script('jquery.autocomplete.js', array('inline' => false));?>
<?php echo $this -> Html -> script('tags', array('inline' => false));?>
<div id="bread-crumbs">
	<?php echo $this->Wizard->progressMenu(array('manufacture'=>'Manufacturer Details', 'variantFeatures'=>'Variant Features', 'attributes'=>'Accessories/Features', 'image'=>'Image', 'review'=> 'Review')); ?>			
</div>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php echo __('Tags')?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<div>
				Fill out the following information to add the collectible.
			</div>
		</div>
		<div class="component-view">
			<div class="collectible add tag">
				<?php echo $this -> Form -> create('Tag', array('url' => '/'.$this->params['controller']. '/'.$this->action.'/tags', )); ?>
				<fieldset>
					<ul class="form-fields">
						<li>
							<div class="label-wrapper">
								<label for="">
									<?php __('Tags') ?>
								</label>
							</div>
							<input type="text" maxlength="25" class="text-box" name="q" id="query" />
							<input type="button" class="button" id="add-query" value="Add"/>
							<?php //echo $this -> Form -> input('tag_id', array('div' => false, 'label' => false));?>
						</li>
					</ul>

				</fieldset>	
				<ul id="add-tag-list" class="tag-list">
					<?php
					$lastKey = 0;
					if(isset($this -> data['Tag'])) {
						foreach($this->data['Tag'] as $key => $tag) {
								echo '<li class="tag">';
								echo $tag['Tag']['tag'];
								echo '<input type="hidden" name="data[CollectiblesTag][' . $key . '][tag]" value="' . $tag['Tag']['tag'] . '"/>';
								echo '</li>';
								$lastKey = $key;
						}
						echo '<script>var lastTagKey =' . $lastKey . ';</script>';
					}
					?>								
				</ul>
				<?php echo $this -> Form -> end(__('Submit', true));?>
			</div>
		</div>
	</div>
</div>