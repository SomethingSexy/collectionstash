<?php


echo $this -> Html -> scriptBlock('var collectibleId =' . $collectibleId.';', array('inline' => false));
echo $this -> Html -> scriptBlock('var uploadDirectory ="' . $this -> FileUpload -> getUploadDirectory().'";', array('inline' => false));

echo $this -> Html -> scriptBlock('var rawCollectible =' . json_encode($collectible) .';', array('inline' => false));
echo $this -> Html -> scriptBlock('var rawBrands =' . json_encode($brands) .';', array('inline' => false));
echo $this -> Html -> scriptBlock('var rawManufacturers =' . json_encode($manufacturers) .';', array('inline' => false));
echo $this -> Html -> scriptBlock('var rawScales =' . json_encode($scales) .';', array('inline' => false));
echo $this -> Html -> scriptBlock('var rawCurrencies =' . json_encode($currencies) .';', array('inline' => false));
echo $this -> Html -> scriptBlock('var rawArtists =' . json_encode($artists) .';', array('inline' => false));
echo $this -> Html -> scriptBlock('var rawCategories =' . json_encode($categories) .';', array('inline' => false));
// echo $this -> Html -> scriptBlock('var rawManufacturesList =' . json_encode($manufacturesList) .';', array('inline' => false));
echo $this -> Html -> scriptBlock('var rawCustomStatuses =' . json_encode($customStatuses) .';', array('inline' => false));
echo $this -> Html -> scriptBlock('var rawVariants =' . json_encode($variants) .';', array('inline' => false));
echo $this -> Html -> scriptBlock('var rawParts =' . json_encode($parts) .';', array('inline' => false));
echo $this -> Html -> scriptBlock('var rawPermissions =' . json_encode($permissions) .';', array('inline' => false));
if (isset($adminMode) && $adminMode) {
	echo $this -> Html -> scriptBlock('var adminMode = true;', array('inline' => false));
} else {
	echo $this -> Html -> scriptBlock('var adminMode = false;', array('inline' => false));
}
if(isset($allowDelete) && $allowDelete){
	echo $this -> Html -> scriptBlock('var allowDelete = true;', array('inline' => false));
} else {
	echo $this -> Html -> scriptBlock('var allowDelete = false;', array('inline' => false));
}
// load these last so we the other data is loaded first!
echo $this -> Minify -> script('pages/page.collectible.edit', array('inline' => false));
echo $this -> Minify -> script('locale', array('inline' => false));
?>
<!-- Each view will get a span -->
<div id="status-container" class="row spacer">

</div>
<div id="directional-text-container" class="row spacer">

</div>
<div id="message-container" class="row spacer">

</div>
<div id="edit-container" class="row">
	<div id="photo-container" class="col-md-4">

	</div>
	<div class="col-md-8">
		<div id="collectible-container">
		

		</div>
		<div id="parts-container">
		

		</div>
	</div>
</div>


<?php echo $this -> element('upload_dialog', array('uploadName' => 'data[CollectiblesUpload][collectible_id]', 'uploadId' => $collectibleId)); ?>
<div id="collectible-delete-dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					×
				</button>
				<h4 id="myModalLabel">Delete Collectible</h4>
			</div>
			<div class="modal-body">
		
			</div>
		
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
					Close
				</button>
				<button class="btn btn-primary save" autocomplete="off">
					Delete
				</button>
			</div>
		</div>
	</div>
</div>

<?php
//Temporary until we get features moved elswhere
$featureAttributeIds = array(2, 4, 20, 3);
foreach ($attributeCategories as $key => $value) {
	if (in_array($value['AttributeCategory']['id'], $featureAttributeIds)) {
		unset($attributeCategories[$key]);
	}

}
// little bit of a hack but this helper is too convienent
echo '<div id="attributes-category-tree" style="display: none">';
echo $this -> Tree -> generate($attributeCategories, array('id' => 'tree', 'model' => 'AttributeCategory', 'element' => 'tree_attribute_node'));
echo '</div>';
?>
