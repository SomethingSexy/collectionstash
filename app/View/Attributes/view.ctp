<div class="component" id="collectible-detail">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Part Details', true); ?></h2>

		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="component-view">
			<div class="detail-wrapper">
				<div class="attribute detail">
					<div class="detail title">
						<h3><?php echo __('Part Details', true); ?></h3>
					</div>
					<dl>
						<dt>
							<?php echo __('Added By'); ?>
						</dt>
						<dd>
							<?php

							if (!empty($attribute['User']['username'])) {
								echo $attribute['User']['username'];
							} else {
								echo '&nbsp;';
							}
 							?>
						</dd>
						<dt>
							<?php echo __('Date Added'); ?>
						</dt>
						<dd>
							<?php
							$datetime = strtotime($attribute['Attribute']['created']);
							$mysqldate = date("m/d/y g:i A", $datetime);
							echo $mysqldate;
							?>
						</dd>
						<?php
							echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'attribute_category_id'), __('Category', true), array('value' =>  $attribute['AttributeCategory']['path_name'], 'compare' => false));
							echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'name'), __('Name', true), array('compare' => false));
							echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Attribute', 'Field' => 'description'), __('Description', true), array('compare' => false));
							echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Manufacture', 'Field' => 'title'), __('Manufacturer', true), array('compare' => false));
							echo $this -> CollectibleDetail -> field($attribute, array('Model' => 'Scale', 'Field' => 'scale'), __('Scale', true), array('compare' => false));
						?>
					</dl>						
					
				</div>
			
				
			</div>

		</div>
	</div>
</div>