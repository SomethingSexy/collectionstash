<?php
echo $this -> Minify -> script('js/jquery.form', array('inline' => false));
echo $this -> Minify -> script('js/jquery.treeview', array('inline' => false));
echo $this -> Minify -> script('js/cs.core.tree', array('inline' => false));
echo $this -> Html -> script('cs.attribute', array('inline' => false));
echo $this -> Html -> script('models/model.status', array('inline' => false));
echo $this -> Html -> script('views/view.status', array('inline' => false));
echo $this -> Html -> script('pages/page.collectible.edit', array('inline' => false));
echo $this -> Minify -> script('js/jquery.iframe-transport', array('inline' => false));
echo $this -> Minify -> script('js/cors/jquery.postmessage-transport', array('inline' => false));
echo $this -> Minify -> script('js/jquery.getimagedata', array('inline' => false));
echo $this -> Minify -> script('js/jquery.fileupload', array('inline' => false));
echo $this -> Minify -> script('js/jquery.fileupload-fp', array('inline' => false));
echo $this -> Minify -> script('js/jquery.fileupload-ui', array('inline' => false));

echo $this -> Minify -> script('js/locale', array('inline' => false));
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
	<i class="icon-upload icon-white"></i>
	<span>{%=locale.fileupload.start%}</span>
	</button>
	{% } %}</td>
	{% } else { %}
	<td colspan="2"></td>
	{% } %}
	<td class="cancel">{% if (!i) { %}
	<button class="btn btn-warning">
	<i class="icon-ban-circle icon-white"></i>
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
	<button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
	<i class="icon-trash icon-white"></i>
	<span>{%=locale.fileupload.destroy%}</span>
	</button>
{% } %}
	</td>
	</tr>
	{% } %}
</script>
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
<div id="directional-text-container" class="row spacer">

</div>
<div id="message-container" class="row spacer">

</div>
<div id="edit-container" class="row">
	<div id="photo-container" span="span4">

	</div>
	<div id="collectible-container" span="span8">
	
	</div>
</div>

<div id="attributes-container" class="row"></div>





<?php echo $this -> element('upload_dialog', array('uploadName' => 'data[CollectiblesUpload][collectible_id]', 'uploadId' => $collectibleId)); ?>
<?php echo $this -> element('attribute_remove_dialog'); ?>
<?php echo $this -> element('attribute_update_dialog'); ?>
<?php echo $this -> element('attribute_remove_link_dialog'); ?>
<?php echo $this -> element('attribute_collectible_add_dialog'); ?>
<?php echo $this -> element('attribute_collectible_update_dialog'); ?>
<?php echo $this -> element('attribute_collectible_add_existing_dialog'); ?>