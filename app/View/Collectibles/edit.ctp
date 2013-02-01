<?php
echo $this -> Minify -> script('js/jquery.form', array('inline' => false));
echo $this -> Minify -> script('js/jquery.treeview', array('inline' => false));
echo $this -> Minify -> script('js/cs.core.tree', array('inline' => false));
echo $this -> Html -> script('cs.attribute', array('inline' => false));
echo $this -> Html -> script('models/model.status', array('inline' => false));
echo $this -> Html -> script('views/view.status', array('inline' => false));
echo $this -> Html -> script('pages/page.collectible.edit', array('inline' => false));


 ?>
<script>
var collectibleId =<?php echo $collectibleId; ?>;
var uploadDirectory = "<?php echo $this -> FileUpload -> getUploadDirectory(); ?>";
<?php if(isset($adminMode) && $adminMode){
	echo 'var adminMode = true;';	
} else {
	echo 'var adminMode = false;';	
}?>

</script>

<!-- Each view will get a span -->
<div id="status-container" class="row spacer">

</div>
<div id="message-container" class="row spacer">

</div>
<div class="row">
	<div id="edit-container" class="span12">
		<div class="row">

		</div>
	</div>
</div>
<div id="attributes-container" class="row"></div>

<div id="tags-container" class="row"></div>

<?php echo $this -> element('upload_dialog', array('uploadName' => 'data[CollectiblesUpload][collectible_id]', 'uploadId' => $collectibleId)); ?>
<?php echo $this -> element('attribute_remove_dialog'); ?>
<?php echo $this -> element('attribute_update_dialog'); ?>
<?php echo $this -> element('attribute_remove_link_dialog'); ?>
<?php echo $this -> element('attribute_collectible_add_dialog'); ?>
<?php echo $this -> element('attribute_collectible_update_dialog'); ?>
<?php echo $this -> element('attribute_collectible_add_existing_dialog'); ?>