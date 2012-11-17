<div id="attribute-add-dialog" class="dialog attribute" title="Add New Part">
	<div class="component component-dialog">
		<div class="inside" >
			<?php echo $this -> element('flash'); ?>
			<div class="component-info">
				<div>
					<p><?php echo __('Use this action to add a brand new part.');?></p>
				</div>
			</div>
			<div class='component-message error'>
				<span></span>
			</div>
			<div class="component-view">
				<div class="attribute-form">
					<?php echo $this -> Form -> create('Attribute', array('data-form-model' => 'Attribute', 'id' => 'AttributeAddForm')); ?>
					<fieldset class="attribute-inputs">
						<legend><?php echo __('Part Details'); ?></legend>
						<ul class="form-fields unstyled">
							<li>
						   <div class="label-wrapper required">
		                        <label for=""> <?php echo __('Category')
		                            ?></label>
		                    </div>
		                    <?php

							echo '<a class="link change-attribute-category-link">' . __('Select') . '</a>';
							echo $this -> Form -> hidden('attribute_category_id');
							?>
		                    </li>  
		                    <li>
								<?php echo $this -> Form -> input('name', array('label' => __('Name'), 'before' => '<div class="label-wrapper">', 'between' => '</div>')); ?>
							</li> 
							<li>
								<?php echo $this -> Form -> input('description', array('label' => __('Description'), 'before' => '<div class="label-wrapper">', 'between' => '</div>')); ?>
							</li> 
							<li>
								<?php echo $this -> Form -> input('manufacture_id', array('empty' => true, 'label' => __('Manufacturer'), 'before' => '<div class="label-wrapper">', 'between' => '</div>')); ?>
							</li>
							<li>
								<?php echo $this -> Form -> input('scale_id', array('empty' => true, 'label' => __('Scale'), 'before' => '<div class="label-wrapper">', 'between' => '</div>')); ?>
							</li>
						</ul>
					</fieldset>
					<?php echo $this -> Form -> end(); ?>
				</div>
				<div class="attribute-category">
					
					<?php
					//Temporary until we get features moved elswhere
					$featureAttributeIds = array(2, 4, 20, 3);
					foreach ($attributeCategories as $key => $value) {
						if (in_array($value['AttributeCategory']['id'], $featureAttributeIds)) {
							unset($attributeCategories[$key]);
						}

					}
					echo $this -> Tree -> generate($attributeCategories, array('id' => 'tree', 'model' => 'AttributeCategory', 'element' => 'tree_attribute_node'));
 ?> 
				</div>
			</div>
		</div>
	</div>
</div>