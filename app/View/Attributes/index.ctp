<div class="widget" id="attribute-component">
	<div class="widget-header">
		<h3><?php echo __('Collectible Parts'); ?></h3>
	</div>
	
	<div class="widget-content">
		<?php echo $this -> element('attributes_search_filters', array('searchUrl' => '/attributes/index')); ?>
		<div class="standard-list attributes index">
			<table class="table table-striped" data-toggle="modal-gallery" data-target="#modal-gallery">
				<?php
				echo '<thead><tr>';
				echo '<th> </td>';
				echo '<td>Photo</td>';
				echo '<th class="category">' . $this -> Paginator -> sort('attribute_category_id', 'Category') . '</th>';
				echo '<th class="name">' . $this -> Paginator -> sort('name', 'Name') . '</th>';
				echo '<th class="description">' . __('Description') . '</th>';
				echo '<th class="manufacturer">' . $this -> Paginator -> sort('manufacture_id', 'Manufacturer') . '</th>';
				echo '<th class="artist">' . $this -> Paginator -> sort('artist_id', 'Artist') . '</th>';
				echo '<th class="scale">' . $this -> Paginator -> sort('scale_id', 'Scale') . '</th>';
				//echo '<th class="actions"> </th>';
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
					$attributeJSON .= '"manufacturerId" : ';
					if (isset($attribute['Attribute']['manufacture_id']) && !is_null($attribute['Attribute']['manufacture_id'])) {
						$attributeJSON .= '"' . $attribute['Attribute']['manufacture_id'] . '",';
					} else {
						$attributeJSON .= '"null" ,';
					}
					$attributeJSON .= '"artistId" : ';
					if (isset($attribute['Attribute']['artist_id']) && !is_null($attribute['Attribute']['artist_id'])) {
						$attributeJSON .= '"' . $attribute['Attribute']['artist_id'] . '",';
					} else {
						$attributeJSON .= '"null" ,';
					}

					$attributeJSON .= '"id" : "' . $attribute['Attribute']['id'] . '"';
					$attributeJSON .= '}';

					$hasCollectibles = false;
					if (count($attribute['AttributesCollectible']) > 0) {
						$hasCollectibles = true;
					}

					$hasCollectibles = ($hasCollectibles) ? 'true' : 'false';

					if (!empty($attribute['AttributesCollectible'])) {
						$popup = '<ul>';
						foreach ($attribute['AttributesCollectible'] as $key => $collectible) {
							if (!empty($collectible['Collectible']['name'])) {
								$popup .= '<li>';
								$popup .= "<a href='/collectibles/view/" . $collectible['Collectible']['id'] . "'>" . $collectible['Collectible']['name'] . "</a>";
								$popup .= '</li>';
							}

						}
						$popup .= '</ul>';
					} else {
						$popup = "<ul class='unstyled'>";
						$popup .= '<li>' . __('Not attached to any collectibles') . '</li>';
						$popup .= '</ul>';
					}

					echo '<tr  data-original-title="' . __('Collectibles Linked to this Item') . '" data-attribute=\'' . $attributeJSON . '\' data-id="' . $attribute['Attribute']['id'] . '"  data-attached="' . $hasCollectibles . '">';
					echo '<td><span title="' . __('Part Information') . '" data-content="' . $popup . '" class="popup"><i class="icon-info-sign"></i></span></td>';
					echo '<td><ul class="thumbnails"><li class="col-md-1">';

					if (!empty($attribute['AttributesUpload'])) {
						foreach ($attribute['AttributesUpload'] as $key => $upload) {
							if ($upload['primary']) {
								echo '<a class="thumbnail" data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false)) . '</a>';
								break;
							}
						}
					} else {
						echo '<a class="thumbnail"><img alt="" src="/img/no-photo.png"></a>';
					}

					echo '</li></ul></td>';

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
					echo '<td>';
					if (!empty($attribute['Attribute']['artist_id'])) {
						echo $attribute['Artist']['name'];
					} else {
						echo 'Not Recorded';
					}

					echo '</td>';
					echo '<td data-id="' . $attribute['Scale']['id'] . '"  class="scale">';
					echo $attribute['Scale']['scale'];
					echo '</td>';
					echo '</tr>';
				}
 ?>
			</tbody></table>
		</div>	
			<p><?php echo $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} parts out of  {:count} total.', true)));?></p>
			<ul class="pagination">
			<?php echo $this -> Paginator -> prev(__('previous', true), array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'disabled')); ?>
			<?php echo $this -> Paginator -> numbers(array('separator' => false, 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'a')); ?>
			<?php echo $this -> Paginator -> next(__('next', true), array('tag' => 'li'), null, array('tag' => 'li', 'disabledTag' => 'a', 'class' => 'disabled')); ?>
			</ul>	
	</div>
</div>
<script>
	$(function() {
		$('.standard-list.attributes.index').children('ul').children('li').children('div.attribute-collectibles').children('a').on('click', function() {
			$(this).parent().parent().children('.collectibles').toggle();
		});

		$('.standard-list.attributes.index > table > tbody> tr > td > span.popup').popover({
			placement : 'bottom',
			html : 'true',
			trigger : 'click'
		});

	}); 
</script>