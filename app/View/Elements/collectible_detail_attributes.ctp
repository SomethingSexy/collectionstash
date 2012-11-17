<?php echo $this -> Minify -> script('js/jquery.form', array('inline' => false)); ?>
<!-- This is eventuall going to have to be used as a component and a dialog when adding from the adding collectible process -->
<?php

echo $this -> Minify -> script('js/jquery.treeview', array('inline' => false));
echo $this -> Minify -> script('js/cs.core.tree', array('inline' => false));
echo $this -> Minify -> script('js/cs.attribute', array('inline' => false));
?>

<?php
if (!isset($adminMode)) {
	$adminMode = false;
}

$lastKey = 0;
$attributeEmpty = empty($collectibleCore['AttributesCollectible']);
?>

<div class="collectible detail attributes">
	<div class="detail title">
		<h3><?php echo __('Parts and Accessories'); ?></h3>
			<?php
			if (isset($showEdit) && $showEdit) {
				echo '<div class="btn-group">
    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
    Action
    <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
		<li><a id="add-new-item-link" class="link" title=' . __('') . '>Add New Item</a>
		</li>
		<li><a id="add-existing-item-link" class="link" title=' . __('') . '>Add Existing Item</a>
		</li>
    </ul>
    </div>';
			}
			?>
	
	</div>
	<?php
	// TO be able to handle editing both the attribute and the attributecollectible easier
	// I am going to put the JSON object as a data attribute on each row
	if ($attributeEmpty) {
		echo '<div class="standard-list attributes collectible empty" data-collectible-id="' . $collectibleCore['Collectible']['id'] . '">';
		echo '<ul>';
		echo '<li>No Parts or Accessories have been added</li>';
		echo '</ul>';
		echo '</div>';
	} else {
		$outputAttribtes = '';
		$added = false;
		foreach ($collectibleCore['AttributesCollectible'] as $key => $attribute) {
			// categoryId
			// categoryName
			// name
			// description
			// scaleId
			// id
			// manufacturerId
			$attributeJSON = '{';
			$attributeJSON .= '"categoryId" : "' . $attribute['Attribute']['AttributeCategory']['id'] . '",';
			$attributeJSON .= '"categoryName" : "' . $attribute['Attribute']['AttributeCategory']['path_name'] . '",';
			$attributeJSON .= '"name" : "' . $attribute['Attribute']['name'] . '",';
			$attributeJSON .= '"description" : "' . $attribute['Attribute']['description'] . '",';
			$attributeJSON .= '"scaleId" : ';
			if (isset($attribute['Attribute']['scale_id']) && !is_null($attribute['Attribute']['scale_id'])) {
				$attributeJSON .= '"' . $attribute['Attribute']['scale_id'] . '",';
			} else {
				$attributeJSON .= '"null" ,';
			}
			$attributeJSON .= '"manufacturerId" : "' . $attribute['Attribute']['manufacture_id'] . '",';
			$attributeJSON .= '"id" : "' . $attribute['Attribute']['id'] . '"';
			$attributeJSON .= '}';

			$attributeCollectibleJSON = '{';
			$attributeCollectibleJSON .= '"id" : "' . $attribute['id'] . '",';
			$attributeCollectibleJSON .= '"attributeId" : "' . $attribute['attribute_id'] . '",';
			$attributeCollectibleJSON .= '"categoryName" : "' . $attribute['Attribute']['AttributeCategory']['path_name'] . '",';
			$attributeCollectibleJSON .= '"count" : "' . $attribute['count'] . '"';
			$attributeCollectibleJSON .= '}';

			$outputAttribtes .= '<tr data-attribute=\'' . $attributeJSON . '\' data-attribute-collectible=\'' . $attributeCollectibleJSON . '\' data-id="' . $attribute['Attribute']['id'] . '"  data-attached="true" data-attribute-collectible-id="' . $attribute['id'] . '">';
			if ($attribute['Attribute']['status_id'] === '2') {
				$outputAttribtes .= '<td><i class="icon-plus"></i></td>';
			} else {
				$outputAttribtes .= '<td></td>';
			}

			$outputAttribtes .= '<td class="category">';

			$outputAttribtes .= $attribute['Attribute']['AttributeCategory']['path_name'] . '</td>';

			$outputAttribtes .= '<td>' . $attribute['Attribute']['name'] . '</td>';

			$outputAttribtes .= '<td>' . $attribute['Attribute']['description'] . '</td>';
			$outputAttribtes .= '<td>' . $attribute['Attribute']['Manufacture']['title'] . '</td>';

			if (isset($attribute['Attribute']['Scale']['scale'])) {
				$outputAttribtes .= '<td>' . $attribute['Attribute']['Scale']['scale'] . '</td>';
			} else {
				$outputAttribtes .= '<td> </td>';
			}

			$outputAttribtes .= '<td class="count">' . $attribute['count'] . '</td>';
			// Going to use the modified date and the last person on the revision who did something to it
			$outputAttribtes .= '<td class="user">' . $attribute['Revision']['User']['username'] . '</td>';
			$outputAttribtes .= '<td class="created">' . $attribute['modified'] . '</td>';
			$outputAttribtes .= '<td class="actions">';
			if (isset($showEdit) && $showEdit) {
				$outputAttribtes .= '<div class="btn-group">
			    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
			    Action
			    <span class="caret"></span>
			    </a>
			    <ul class="dropdown-menu">
					<li><a class="link edit-attribute-collectible-link" title=' . __('Edit Collectible Part<') . '>Edit Collectible Part</a>
					</li>
					<li><a class="link edit-attribute-link" title=' . __('Edit Part') . '>Edit Part</a>
					</li>
					<li><a class="link remove-link" title=' . __('Remove Collectible Part') . '>Remove Collectible Part</a>
					</li>
					<li><a class="link remove-attribute" title=' . __('Remove Part') . '>Remove Part</a>
					</li>
			    </ul>
			    </div>';
			} else {
				$outputAttribtes .= ' ';
			}

			$outputAttribtes .= '</td>';

			$outputAttribtes .= '</tr>';
			$added = true;
		}

		if ($added) {
			echo '<div class="standard-list attributes collectible" data-collectible-id="' . $collectibleCore['Collectible']['id'] . '">';
			echo '<table class="table table-striped">';
			echo '<thead><tr>';
			echo '<th></th>';
			echo '<th class="category">' . __('Category') . '</th>';
			echo '<th>' . __('Name', true) . '</th>';
			echo '<th>' . __('Description', true) . '</th>';
			echo '<th>' . __('Manufacturer', true) . '</th>';
			echo '<th>' . __('Scale', true) . '</th>';
			echo '<th title="' . __('The amount of items of this type this collectible has.') . '" class="count">' . __('Count', true) . '</th>';
			echo '<th class="user" title="' . __('The user who performed the last action on this item.') . '">' . __('Added By') . '</th>';
			echo '<th class="created">' . __('Last Modified') . '</th>';
			echo '<th class="actions"> </th>';
			echo '</tr></thead>';
			echo '<tbody>';
			echo $outputAttribtes;
			echo '</tbody>';
			echo '</table>';
			echo '</div>';
		} else {
			echo '<div class="attributes-list empty" data-collectible-id="' . $collectibleCore['Collectible']['id'] . '">';
			echo '<ul>';
			echo '<li>No Accessories/Features have been added</li>';
			echo '</ul>';
			echo '</div>';
		}
	}
	?>
</div>

