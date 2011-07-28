<?php echo $this -> Html -> script('attributes-edit', array('inline' => false));?>
<div class="component" id="collectible-add-component">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php echo __('Edit Collectible Attributes')?>
			</h2>
		</div>
		<?php echo $this -> element('flash');?>
		<?php 
			if(isset($errors)) {
				echo $this -> element('errors', array('errors' => $errors));	
			}
		?>
		<div class="component-info">
			<div>
				
			</div>
		</div>
		<div class="component-view">
			<div class="collectible add">
				<?php echo $this -> Form -> create('Collectible', array('id'=>'add-attributes-form', 'url'=>'/attributesCollectibles/edit/'.$collectibleId)); ?>
				<fieldset>
					<legend>
						<?php __('Part Break Down');?>
					</legend>
					<div id="collectible-attributes-list" class="attributes-list">
						<ul>
							<?php
							$lastKey = 0;
							echo '<li class="title">';
							echo '<span class="attribute-name">'.__('Part', true).'</span>';
							echo '<span class="attribute-description">'.__('Description', true).'</span>';
							echo '<span class="attribute-action">'.__('Action', true).'</span>';
							echo '</li>';
							if(isset($this -> data)) {

								foreach($this -> data as $key => $attribue) {
									if($attribue['AttributesCollectible']['variant'] !== '1') {
										echo '<li>';
										echo '<span class="attribute-name">';
										echo $attribue['Attribute']['name'];
										echo '</span>';
										echo '<span class="attribute-description">';
										echo $attribue['AttributesCollectible']['description'];
										echo '</span>';
										echo '<span class="attribute-action"><a class="remove-attribute">Remove</a></span>';
										echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][id]" value="' . $attribue['AttributesCollectible']['id'] . '"/>';
										echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][attribute_id]" value="' . $attribue['AttributesCollectible']['attribute_id'] . '"/>';
										echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][description]" value="' . $attribue['AttributesCollectible']['description'] . '"/>';
										echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][name]" value="' . $attribue['Attribute']['name'] . '"/>';
										echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][variant]" value="' . $attribue['AttributesCollectible']['variant'] . '"/>';
										if(isset($attribue['AttributesCollectible']['action'] )) {
											echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][action]" value="' . $attribue['AttributesCollectible']['action'] . '"/>';	
										} else {
											echo '<input type="hidden" class="attribute action" name="data[AttributesCollectible][' . $key . '][action]" value=""/>';
										} 
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
						<a class="add-attribute">Add Attribute</a>
					</div>
				</fieldset>	

				<?php echo $this -> Form -> end();?>
				
				<?php echo $this -> Form -> create('Collectible', array('url' => '/'.$this->params['controller']. '/'.$this->action.'/attributes' ,'id'=>'skip-attributes-form'));?>
					<input type="hidden" name="data[skip]" value="true" />
				</form>
				<div class="links">
					<input type="button" id="add-attributes-button" class="button" value="Submit">
					<input type="button" id="skip-attributes-button" class="button" value="Cancel">
				</div>
				<script>
					$(function(){
						//Eh move this out of here
						$('#add-attributes-button').click(function(){
							$('#add-attributes-form').submit();	
						});	
						$('#skip-attributes-button').click(function(){
							$('#skip-attributes-form').submit();	
						});						
					});

				</script>
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
<div id="add-attribute-dialog" class="dialog" title="Add Attribute">
	<div class="component">
		<div class="inside" >
			<div class="component-info">
				<div>
					<?php __('Fill out the information below to add an Attribute to this variant.') ?>
				</div>
			</div>
			<div class="component-view">
				<?php echo $this -> Form -> create('AttributesCollectible', array('url' => '')); ?>
				<fieldset>
					<ul id="add-attribute-dialog-fields" class="form-fields">
						<li id="description-field">
							<div class="label-wrapper">
								<label for="CollectibleName">
									<?php __('Description') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('description', array('maxlength' => 50, 'id' => 'attributeDescription', 'div' => false, 'label' => false, 'error' => false));?>
						</li>
					</ul>
				</fieldset>
				<?php echo $this -> Form -> end();?>
			</div>
		</div>
	</div>
</div>