<?php echo $this -> Html -> script('attributes-add', array('inline' => false));?>
<div id="bread-crumbs">
	<?php echo $this->Wizard->progressMenu(array('manufacture'=>'Manufacturer Details', 'attributes'=>'Accessories/Features', 'tags'=>'Tags','image'=>'Image', 'review'=> 'Review')); ?>			
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
		<?php 
			if(isset($errors)) {
				echo $this -> element('errors', array('errors' => $errors));	
			}
		?>
		<div class="component-info">
			<div>
				<?php echo __('Add any accessories or features for this collectible.  This can be used to break out individual parts or specific features about this collectible.')?>
			</div>
		</div>
		<div class="component-view">
			<div class="collectible add">
				<?php echo $this -> Form -> create('Collectible', array('id'=>'add-attributes-form', 'url' => '/'.$this->params['controller']. '/'.$this->action.'/attributes', )); ?>
				<fieldset>
					<legend>
						<?php echo __('Accessories/Features');?>
					</legend>
					<div id="collectible-attributes-list" class="attributes-list">
						<ul>
							<?php
							$lastKey = 0;
							echo '<li class="title">';
							echo '<span class="attribute-name">'.__('Name', true).'</span>';
							echo '<span class="attribute-description">'.__('Description', true).'</span>';
							echo '<span class="attribute-action">'.__('Action', true).'</span>';
							echo '</li>';
							if(isset($this -> data['AttributesCollectible'])) {
								foreach($this->data['AttributesCollectible'] as $key => $attribue) {
									echo '<li>';
									echo '<span class="attribute-name">';
									echo $attribue['name'];
									echo '</span>';
									echo '<span class="attribute-description">';
									echo $attribue['description'];
									echo '</span>';
									echo '<span class="attribute-action"><a class="remove-attribute">Remove</a></span>';
									echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][attribute_id]" value="' . $attribue['attribute_id'] . '"/>';
									echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][description]" value="' . $attribue['description'] . '"/>';
									echo '<input type="hidden" name="data[AttributesCollectible][' . $key . '][name]" value="' . $attribue['name'] . '"/>';
									echo '</li>';
									$lastKey = $key;

								}
								echo '<script>var lastAttributeKey =' . $lastKey . ';</script>';
							}
							?>
						</ul>
					</div>
					<div class="links">
						<a class="add-attribute link">Add Attribute</a>
					</div>
				</fieldset>	
				<input type="hidden" name="data[dummy]" value="" />
				<?php echo $this -> Form -> end();?>
				
				<?php echo $this -> Form -> create('Collectible', array('url' => '/'.$this->params['controller']. '/'.$this->action.'/attributes' ,'id'=>'skip-attributes-form'));?>
					<input type="hidden" name="data[skip]" value="true" />
				</form>
				<div class="links">
					<input type="button" id="add-attributes-button" class="button" value="Submit">
					<input type="button" id="skip-attributes-button" class="button" value="Skip">
				</div>
			</div>
		</div>
	</div>
</div>
<?php if($this -> Session -> check('add.collectible.variant')) { ?>
<div id="base-collectible" class="dialog">
<?php      
	echo $this->element('collectible_detail_attributes', array(
		'collectibleCore' => $collectible
	));
?>
</div>
<?php } ?>
<script>
	$(function(){
		//Eh move this out of here
		$(".ui-icon-info").tooltip({
			position: 'center right',
			opacity: 0.7
		});
		$('#add-attributes-button').click(function(){
			$('#add-attributes-form').submit();	
		});	
		$('#skip-attributes-button').click(function(){
			$('#skip-attributes-form').submit();	
		});	
		$('#base-collectible').dialog({
			'autoOpen' : false,
			'width' : 525,
			'autoHeight': true,
			'resizable': false,
			'modal': true
		});	
		$('#base-collectible-link').click(function(){
			$('#base-collectible').dialog('open');
		});								
	});

</script>
<div id="add-attribute-dialog" class="dialog" title="Add Attribute">
	<div class="component">
		<div class="inside" >
			<div class="component-info">
				<div>
					<?php echo __('Fill out the information below to add an Attribute to this variant.') ?>
				</div>
			</div>
			<div class="component-view">
				<?php echo $this -> Form -> create('AttributesCollectible', array('url' => '')); ?>
				<fieldset>
					<ul id="add-attribute-dialog-fields" class="form-fields dialog-fields">
						<li id="description-field">
							<div class="label-wrapper">
								<label for="CollectibleName">
									<?php echo __('Description') ?>
								</label>
							</div>
							<?php echo $this -> Form -> input('description', array('maxlength' => 200, 'id' => 'attributeDescription', 'div' => false, 'label' => false, 'error' => false));?>
						</li>
					</ul>
				</fieldset>
				<?php echo $this -> Form -> end();?>
			</div>
		</div>
	</div>
</div>