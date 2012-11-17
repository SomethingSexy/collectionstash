<?php echo $this -> Minify -> script('js/jquery.form', array('inline' => false)); ?>
<!-- This is eventuall going to have to be used as a component and a dialog when adding from the adding collectible process -->
<?php
echo $this -> Minify -> script('js/jquery.treeview', array('inline' => false));
echo $this -> Minify -> script('js/cs.core.tree', array('inline' => false));
echo $this -> Html -> script('cs.attribute', array('inline' => false));
?>

<!-- Eventually show the image of the part 
	Add filters to this page as well -->
<div class="component" id="attribute-component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Collectible Parts'); ?></h2>
			<div class="btn-group">
			    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
			    Action
			    <span class="caret"></span>
			    </a>
			    <ul class="dropdown-menu">
					<li><a id="add-new-item-link" class="link">Add New Item</a></li>
			    </ul>
		    </div>
		</div>
		<div class="component-view">
		<?php echo $this -> element('attributes_search_filters', array('searchUrl' => '/attributes/index')); ?>
		<div class="standard-list attributes index">
			<table class="table table-striped">
				<?php
				echo '<thead><tr>';
				echo '<th class="category">' . $this -> Paginator -> sort('attribute_category_id', 'Category') . '</th>';
				echo '<th class="name">' . $this -> Paginator -> sort('name', 'Name') . '</th>';
				echo '<th class="description">' . __('Description') . '</th>';
				echo '<th class="manufacturer">' . $this -> Paginator -> sort('manufacture_id', 'Manufacturer') . '</th>';
				echo '<th class="scale">' . $this -> Paginator -> sort('scale_id', 'Scale') . '</th>';
				echo '<th class="created">' . $this -> Paginator -> sort('created', 'Created') . '</th>';
				echo '<th class="actions"> </th>';
				echo '</tr></thead><tbody>';
				foreach ($attributes as $attribute) {
					$attributeJSON = '{';
					$attributeJSON .= '"categoryId" : "' . $attribute['AttributeCategory']['id'] . '",';
					$attributeJSON .= '"categoryName" : "' . $attribute['AttributeCategory']['path_name'] . '",';
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

					$hasCollectibles = false;
					if (count($attribute['AttributesCollectible']) > 0) {
						$hasCollectibles = true;
					}

					$hasCollectibles = ($hasCollectibles) ? 'true' : 'false';

					$popup = '<ul>';
					if (!empty($attribute['AttributesCollectible'])) {
						foreach ($attribute['AttributesCollectible'] as $key => $collectible) {
							$popup .= '<li>';
							$popup .= $collectible['Collectible']['name'];
							$popup .= '</li>';
						}
					} else {
						$popup .= '<li>' . __('None') . '</li>';
					}

					$popup .= '</ul>';

					echo '<tr data-content="' . $popup . '" data-original-title="' . __('Collectibles Linked to this Item') . '" data-attribute=\'' . $attributeJSON . '\' data-id="' . $attribute['Attribute']['id'] . '"  data-attached="' . $hasCollectibles . '">';
					echo '<td data-id="' . $attribute['AttributeCategory']['id'] . '" class="category">';
					echo $attribute['AttributeCategory']['path_name'];
					echo '</td>';
					echo '<td class="name">';
					if (empty($attribute['Attribute']['name'])) {

					} else {
						echo $attribute['Attribute']['name'];
					}

					echo '</td>';
					echo '<td class="description">';
					echo $attribute['Attribute']['description'];
					echo '</td>';
					echo '<td data-id="' . $attribute['Manufacture']['id'] . '" class="manufacturer">';
					echo $attribute['Manufacture']['title'];
					echo '</td>';
					echo '<td data-id="' . $attribute['Scale']['id'] . '"  class="scale">';
					echo $attribute['Scale']['scale'];
					echo '</td>';
					echo '<td class="created">';
					echo $attribute['Attribute']['created'];
					echo '</td>';
					echo '<td class="actions">';

					if (isset($isLoggedIn) && $isLoggedIn === true) {
						echo '<div class="btn-group">';
						echo '<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">Action<span class="caret"></span></a>';
						echo '<ul class="dropdown-menu">';
						echo '<li><a class="link edit-attribute-link" title=' . __('Edit Part') . '>Edit Part</a>';
						echo '</li>';
						echo '<li><a class="link remove-attribute" title=' . __('Remove Part') . '>Remove Part</a>';
						echo '</li>';
						echo '</ul>';
						echo '</div>';
					}

					echo '</td>';
					echo '</tr>';
				}
 ?>
			</tbody></table>
		</div>	
		<div class="paging">
			<p>
				<?php
				echo $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} parts out of  {:count} total.', true)));
				?>
			</p>
			<?php

			$urlparams = $this -> request -> query;
			unset($urlparams['url']);
			$this -> Paginator -> options(array('url' => array('?' => http_build_query($urlparams))));

			echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));
			?>
			<?php echo $this -> Paginator -> numbers(array('separator' => false)); ?>
			<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled')); ?>
		</div>		
		</div>
	</div>
</div>
<script>
	$(function() {
		$('.standard-list.attributes.index').children('ul').children('li').children('div.attribute-collectibles').children('a').on('click', function() {
			$(this).parent().parent().children('.collectibles').toggle();
		});

		var removeAttributes = new RemoveAttributes({
			$element : $('.standard-list.attributes')
		});
		removeAttributes.init();
		
		var updateAttributes = new UpdateAttributes({
			$element : $('.standard-list.attributes')
		});
		updateAttributes.init();
		
		var addAttributes = new AddAttributes({
			
		});
		
		addAttributes.init();

		$('.standard-list.attributes.index > table > tbody> tr').popover({
			placement : 'bottom',
			html : 'true',
			trigger : 'hover'
		});

	});
</script>

<?php echo $this -> element('attribute_remove_dialog'); ?>
<?php echo $this -> element('attribute_update_dialog'); ?>
<?php echo $this -> element('attribute_add_dialog'); ?>
