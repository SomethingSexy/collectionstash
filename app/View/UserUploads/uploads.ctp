<?php echo $this -> Minify -> script('js/jquery.form', array('inline' => false)); ?>
<?php
echo $this -> Minify -> script('js/jquery.iframe-transport', array('inline' => false));
echo $this -> Minify -> script('js/cors/jquery.postmessage-transport', array('inline' => false));
echo $this -> Minify -> script('js/jquery.getimagedata', array('inline' => false));
echo $this -> Minify -> script('js/jquery.fileupload', array('inline' => false));
echo $this -> Minify -> script('js/jquery.fileupload-fp', array('inline' => false));
echo $this -> Minify -> script('js/jquery.fileupload-ui', array('inline' => false));

echo $this -> Minify -> script('js/locale', array('inline' => false));
?>
<h2><?php echo $username . '\'s' .__(' Photos', true)?></h2>
<div id="user-uploads-component" class="widget">
		<div class="widget-header">
			<i class="icon-camera"></i><h3>Photos</h3>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="widget-content">
			<div class="stats">
				<span class="count"><?php echo __('Images') . ': <span id="user-count">' . $uploadCount . '</span>/' . Configure::read('Settings.User.uploads.total-allowed'); ?></span>
			</div>
			<form id="fileupload" action="server/php/" method="POST" enctype="multipart/form-data">
				<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
				<div class="row fileupload-buttonbar">
					<div class="col-md-7">
						<!-- The fileinput-button span is used to style the file input field as button -->
						<span class="btn btn-success fileinput-button"> <i class="icon-plus icon-white"></i> <span>Add files...</span>
							<input type="file" name="data[UserUpload][file]" multiple>
						</span>
						<!--<button type="submit" class="btn btn-primary start">
						<i class="icon-upload icon-white"></i>
						<span>Start upload</span>
						</button>
						<button type="reset" class="btn btn-warning cancel">
						<i class="icon-ban-circle icon-white"></i>
						<span>Cancel upload</span>
						</button>
						<button type="button" class="btn btn-danger delete">
						<i class="icon-trash icon-white"></i>
						<span>Delete</span>
						</button>
						<input type="checkbox" class="toggle">-->
					</div>
					<!-- The global progress information -->
					<div class="col-md-5 fileupload-progress fade">
						<!-- The global progress bar -->
						<div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
							<div class="bar" style="width:0%;"></div>
						</div>
						<!-- The extended global progress information -->
						<div class="progress-extended">
							&nbsp;
						</div>
					</div>
				</div>
				<!-- The loading indicator is shown during file processing -->
				<div class="fileupload-loading"></div>
				<br>
				<div id="dropzone" class="fade well">
					Drop files here
				</div>
				<br>
				<!-- The table listing the files available for upload/download -->
				<table role="presentation" class="table table-striped">
					<tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
				</table>
			</form>
		</div>
</div>
<script>
		$(function() {

	$('#fileupload').fileupload({
		dropZone : $('#dropzone'),
		 error: function (jqXHR, textStatus, errorThrown) {
        // Called for each failed chunk upload
	    },
	    success: function (data, textStatus, jqXHR) {
	        // Called for each successful chunk upload
	    }
	});
	$('#fileupload').bind('fileuploaddone', function (e, data) {
		console.log(data.result);	
	});
	
	$('#fileupload').fileupload('option', 'redirect', window.location.href.replace(/\/[^\/]*$/, '/cors/result.html?%s'));

	$('#fileupload').fileupload('option', {
		url : '/user_uploads/add',
		maxFileSize : 2097152,
		maxNumberOfFiles : <?php echo Configure::read('Settings.User.uploads.total-allowed'); ?>,
		acceptFileTypes : /(\.|\/)(gif|jpe?g|png)$/i,
		process : [{
				action : 'load',
				fileTypes : /^image\/(gif|jpeg|png)$/,
				maxFileSize : 2097152 // 2MB
			}, {
				action : 'resize',
				maxWidth : 1440,
				maxHeight : 900
			}, {
				action : 'save'
		}]
	});

	var that = $('#fileupload');
	that.fileupload('option', 'done').call(that, null, {
		result : <?php echo $this -> Js -> object($uploads); ?>
	});
	
	$('#fileupload').on('fileuploadadd', function (e, data) {
	
	});


});

</script>
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
	<td class="edit">
	<a class="btn" href="{%=file.edit_url%}">
	<i class="icon-pencil icon-white"></i>
	<span>Edit</span>
	</a>
	</td>
	{% } %}
	<td class="delete">
         <button class="btn btn-danger delete" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                <i class="icon-trash icon-white"></i>
                <span>Delete</span>
            </button>
	</td>
	</tr>
	{% } %}
</script>