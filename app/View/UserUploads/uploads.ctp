<?php echo $this -> Minify -> script('jquery.form', array('inline' => false)); ?>
<?php
echo $this -> Html -> script('/bower_components/blueimp-file-upload/js/jquery.iframe-transport', array('inline' => false));
echo $this -> Html -> script('/bower_components/blueimp-file-upload/js/cors/jquery.postmessage-transport', array('inline' => false));
// echo $this -> Minify -> script('jquery.getimagedata', array('inline' => false));
echo $this -> Html -> script('/bower_components/blueimp-file-upload/js/jquery.fileupload', array('inline' => false));
echo $this -> Html -> script('/bower_components/blueimp-file-upload/js/jquery.fileupload-process', array('inline' => false));
echo $this -> Html -> script('/bower_components/blueimp-file-upload/js/jquery.fileupload-image', array('inline' => false));
echo $this -> Html -> script('/bower_components/blueimp-file-upload/js/jquery.fileupload-validate', array('inline' => false));
echo $this -> Html -> script('/bower_components/blueimp-file-upload/js/jquery.fileupload-ui', array('inline' => false));

echo $this -> Minify -> script('locale', array('inline' => false));
?>
<h2><?php echo $username . '\'s' .__(' Photos', true)?></h2>
<div id="user-uploads-component" class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"><i class="fa fa-camera"></i> Photos</h3>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="panel-body">
			<div class="btn-group actions">
				<?php
					echo '<a title="Share and Edit Photos" class="btn" href="/user_uploads/edit"><i class="fa fa-pencil-square-o"></i> Share and Edit Photos</a>';
				?>
			</div>
			<div class="stats pull-right">
				<span class="count"><?php echo __('Images') . ': <span id="user-count">' . $uploadCount . '</span>/' . Configure::read('Settings.User.uploads.total-allowed'); ?></span>
			</div>
			<form id="fileupload" action="server/php/" method="POST" enctype="multipart/form-data">
				<!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
				<div class="row fileupload-buttonbar">
					<div class="col-md-7">
						<!-- The fileinput-button span is used to style the file input field as button -->
						<span class="btn btn-success fileinput-button"> <i class="fa fa-plus"></i> <span>Add files...</span>
							<input type="file" name="data[UserUpload][file]" multiple>
						</span>
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
				<!--<div id="dropzone" class="fade well">
					Drop files here
				</div>-->
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

    var $fileupload = $('#fileupload', this.el);
    $fileupload.fileupload({
        url : '/user_uploads/add',
        dataType: 'json',
        // maxFileSize: 2097152,
        maxNumberOfFiles : <?php echo Configure::read('Settings.User.uploads.total-allowed'); ?>,
        acceptFileTypes: /(\.|\/)(gif|jpe?g|png)$/i,
        disableImageResize: /Android(?!.*Chrome)|Opera/.test(window.navigator && navigator.userAgent),
        previewMaxWidth: 100,
        previewMaxHeight: 100,
        previewCrop: true,
        autoUpload: false
    });
    $('#fileupload').fileupload('option', {
        maxFileSize: 5000000,

    });    

    $fileupload.fileupload('option', 'done').call($fileupload, $.Event('done'), {
        result: <?php echo $this -> Js -> object($uploads); ?>
    });


});

</script>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                    <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
            {% } else { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>