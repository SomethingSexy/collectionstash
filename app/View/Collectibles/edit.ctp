<?php
// echo $this -> Minify -> script('jquery.form', array('inline' => false));
// echo $this -> Minify -> script('jquery.treeview', array('inline' => false));
// echo $this -> Minify -> script('cs.core.tree', array('inline' => false));
echo $this -> Minify -> script('cs.attribute', array('inline' => false));
// echo $this -> Minify -> script('models/model.collectible', array('inline' => false));
// echo $this -> Minify -> script('models/model.status', array('inline' => false));
// echo $this -> Minify -> script('views/view.status', array('inline' => false));
// echo $this -> Minify -> script('views/view.alert', array('inline' => false));
// echo $this -> Minify -> script('views/view.collectible.delete', array('inline' => false));
// echo $this -> Minify -> script('thirdparty/backbone.bootstrap-modal', array('inline' => false));
echo $this -> Minify -> script('pages/page.collectible.edit', array('inline' => false));
// echo $this -> Minify -> script('jquery.iframe-transport', array('inline' => false));
// echo $this -> Minify -> script('cors/jquery.postmessage-transport', array('inline' => false));
// echo $this -> Minify -> script('jquery.getimagedata', array('inline' => false));
// echo $this -> Minify -> script('jquery.fileupload', array('inline' => false));
// echo $this -> Minify -> script('jquery.fileupload-fp', array('inline' => false));
// echo $this -> Minify -> script('jquery.fileupload-ui', array('inline' => false));

echo $this -> Minify -> script('locale', array('inline' => false));
?>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
	{% for (var i=0, file; file=o.files[i]; i++) { %}
	<tr class="template-upload fade">
	<td class="preview"><span class="fade"></span></td>
	<td class="name"><span>{%=file.name%}</span></td>
	<td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
	{% if (file.error) { %}
	<td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
	{% } else if (o.files.valid && !i) { %}
	<td>
	<div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
	</td>
	<td class="start">{% if (!o.options.autoUpload) { %}
	<button class="btn btn-primary">
	<i class="fa fa-upload icon-white"></i>
	<span>{%=locale.fileupload.start%}</span>
	</button>
	{% } %}</td>
	{% } else { %}
	<td colspan="2"></td>
	{% } %}
	<td class="cancel">{% if (!i) { %}
	<button class="btn btn-warning">
	<i class="fa fa-ban icon-white"></i>
	<span>{%=locale.fileupload.cancel%}</span>
	</button>
	{% } %}</td>
	</tr>
	{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
	{% for (var i=0, file; file=o.files[i]; i++) { %}
	<tr class="template-download fade">
	{% if (file.error) { %}
	<td></td>
	<td class="name"><span>{%=file.name%}</span></td>
	<td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
	<td class="error" colspan="2"><span class="label label-important">{%=locale.fileupload.error%}</span> {%=locale.fileupload.errors[file.error] || file.error%}</td>
	{% } else { %}
	<td class="preview">{% if (file.thumbnail_url) { %}
	<a href="{%=file.url%}" title="{%=file.name%}" data-gallery="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
	{% } %}</td>
	<td class="name">
	<a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
	</td>
	<td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
	<td colspan="2"><span>{% if(file.pending) { %} {%=file.pendingText %} {% } %}</span></td>
	{% } %}
	<td class="delete">
	{% if(file.allowDelete){ %}
	<button class="btn btn-danger delete" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
	<i class="fa fa-trash-o icon-white"></i>
	<span>Delete</span>
	</button>
	{% } %}
	</td>
	</tr>
	{% } %}
</script>
<script>
	var collectibleId =<?php echo $collectibleId; ?>;
	var uploadDirectory =  "<?php echo $this -> FileUpload -> getUploadDirectory(); ?>";
	<?php
	if (isset($adminMode) && $adminMode) {
		echo 'var adminMode = true;';
	} else {
		echo 'var adminMode = false;';
	}
	if(isset($allowDelete) && $allowDelete){
		echo 'var allowDelete = true;';
	} else {
		echo 'var allowDelete = false;';
	}
?></script>

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
	<div id="collectible-container" class="col-md-8">
		

	</div>
</div>

<div id="attributes-container" class="row well"></div>

<?php echo $this -> element('upload_dialog', array('uploadName' => 'data[CollectiblesUpload][collectible_id]', 'uploadId' => $collectibleId)); ?>
<?php echo $this -> element('attribute_remove_link_dialog'); ?>
<?php echo $this -> element('attribute_collectible_update_dialog'); ?>
<!-- TODO Update this so we only have one modal -->
<div id="attribute-collectible-add-existing-dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					×
				</button>
				<h4 id="myModalLabel">Add Existing Collectible Part</h4>
			</div>
			<div class="modal-body">
		
			</div>
		
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
					Close
				</button>
				<button class="btn btn-primary save" autocomplete="off">
					Add
				</button>
			</div>
		</div>
	</div>
</div>
<div id="attribute-collectible-add-new-dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					×
				</button>
				<h4 id="myModalLabel">Add New Collectible Part</h4>
			</div>
			<div class="modal-body">
		
			</div>
		
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
					Close
				</button>
				<button class="btn btn-primary save" autocomplete="off">
					Add
				</button>
			</div>
		</div>
	</div>
</div>

<div id="attribute-update-dialog" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
					×
				</button>
				<h4 id="myModalLabel">Edit Part</h4>
			</div>
			<div class="modal-body">
		
			</div>
		
			<div class="modal-footer">
				<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">
					Close
				</button>
				<button class="btn btn-primary save" autocomplete="off">
					Submit
				</button>
			</div>
		</div>
	</div>
</div>

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
