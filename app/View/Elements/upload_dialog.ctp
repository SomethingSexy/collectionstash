<?php
echo $this -> Minify -> script('js/jquery.iframe-transport', array('inline' => false));
echo $this -> Minify -> script('js/cors/jquery.postmessage-transport', array('inline' => false));
echo $this -> Minify -> script('js/jquery.getimagedata', array('inline' => false));
echo $this -> Minify -> script('js/jquery.fileupload', array('inline' => false));
echo $this -> Minify -> script('js/jquery.fileupload-fp', array('inline' => false));
echo $this -> Minify -> script('js/jquery.fileupload-ui', array('inline' => false));

echo $this -> Minify -> script('js/locale', array('inline' => false));
echo $this -> Html -> script('cs.upload', array('inline' => false));
?>
<script>
		$(function() {
			//TODO: Need to fix this for user uploads
			// $('#fileupload').balls({
				// 'collectibleId' : <?php echo $uploadId; ?>,
				// 'element' : '#upload-link'
			// });
		});

		$(document).bind('drop', function(e) {
			var url = $(e.originalEvent.dataTransfer.getData('text/html')).filter('img').attr('src');
			if (url) {
				$.getImageData({
					url : url,
					success : function(img) {
						var canvas = document.createElement("canvas");
						canvas.width = img.width;
						canvas.height = img.height;
						if (canvas.getContext && canvas.toBlob) {
							canvas.getContext("2d").drawImage(img, 0, 0, img.width, img.height);
							canvas.toBlob(function(blob) {
								$("#fileupload").fileupload("add", {
									files : [blob]
								});
							}, "image/jpeg");
						}
					}
				});
			}
		});
		$(document).bind('dragover', function(e) {
			var dropZone = $('#dropzone'), timeout = window.dropZoneTimeout;
			if (!timeout) {
				dropZone.addClass('in');
			} else {
				clearTimeout(timeout);
			}
			if (e.target === dropZone[0]) {
				dropZone.addClass('hover');
			} else {
				dropZone.removeClass('hover');
			}
			window.dropZoneTimeout = setTimeout(function() {
				window.dropZoneTimeout = null;
				dropZone.removeClass('in hover');
			}, 100);
		});
</script>
<div id="upload-dialog" class="dialog attribute" title="Add Photo">
	<div class="component component-dialog">
		<div class="inside" >
			<?php echo $this -> element('flash'); ?>
			<div class="component-info">
				<div>
					<?php echo __('This will allow you to sumbit photos for the collectible you are viewing.  Each photo you submit will require approval from an admin.  All pending photos are indicated below.  If you submitted an incorrect photo and it is still pending you can delete it, otherwise you can also submit for approval the removal of a photo.  Note, you can only delete pending photos that you added.  Currently, you cannot delete the primary photo.')
					?>
					<p><?php echo __('Image requirements:'); ?></p>
					<ul>
						<li><?php echo __('The image must be less than 2MB.'); ?></li>
					</ul>
					<p><?php echo __('Image recommendations:'); ?></p>
					<ul>
						<li><?php echo __('The image should be at least 150 x 150 pixels.'); ?></li>
						<li><?php echo __('This will be used as the default image for this collectible.  Thumbnails will look best if this image\'s height is bigger than it\'s width.'); ?></li>
						<li><?php echo __('Please try and use a professionally shot photo.'); ?></li>
					</ul>
				</div>
			</div>
			<div class='component-message error'>
				<span></span>
			</div>
			<div class="component-view">
				<!-- The file upload form used as target for the file upload widget -->
				<form id="fileupload" action="server/php/" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="<?php echo $uploadName; ?>" value="<?php echo $uploadId; ?>" />
					<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
					<div class="row fileupload-buttonbar">
						<div class="span7">
							<!-- The fileinput-button span is used to style the file input field as button -->
							<span class="btn btn-success fileinput-button"> <i class="icon-plus icon-white"></i> <span>Add files...</span>
								<input type="file" name="data[Upload][file]" multiple>
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
						<div class="span5 fileupload-progress fade">
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
	</div>
</div>
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
