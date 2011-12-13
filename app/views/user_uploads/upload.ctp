<?php echo $this -> Html -> script('jquery.form', array('inline' => false));?>
<div id="user-uploads-component" class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $username . '\'s' .__(' uploads', true)
			?></h2>
			<div class="actions">
				<ul></ul>
			</div>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-view">
			<div class="upload-component">
				<form enctype="multipart/form-data" method="POST" action="/user_uploads/addUpload.json" id="uploadForm" encoding="multipart/form-data">
					<label>Image:</label>
					<input type="hidden" value="2097152" name="MAX_FILE_SIZE">
					<input id="upload-add-field" class="image-field" type="file" name="data[UserUpload][file]">
					<input id="upload-add-button" class="add-button" type="submit" value="Add">
				</form>
				<div id="upload-error" class="upload-error">
					<span class="error-message"></span>
				</div>
			</div>
			<div id="images" class="images">
				<?php

				foreach ($uploads as $key => $value) {
					echo '<div class="image">';
					echo $fileUpload -> image($value['UserUpload']['name'], array('width' => 100, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $value['UserUpload']['user_id']));
					echo '</div>';
				}
				?>
			</div>
		</div>
	</div>
</div>
<script>
	$(function() {
		var biggestHeight = 0;
		$('#images .image img').each(function() {
			var imgHeight = $(this).height();
			if(imgHeight > biggestHeight) {
				biggestHeight = imgHeight;
			}
		});
		$('#images .image').css('min-height', biggestHeight);
		$('#uploadForm').ajaxForm({
			dataType : 'json',
			beforeSubmit : function(a, f, o) {
				$('#upload-error').children('span.error-message').text('');
				if($('#upload-add-field').val() === '') {
					return false;
				} else {
					var $img = $('<img></img').attr('src', '/img/ajax-loader-circle.gif');
					var $imgWrapper = $('<div></div>').addClass('image-loader').addClass('image');
					$imgWrapper.append($img);
					$('#images').prepend($imgWrapper);
					$('#upload-add-button').attr('disabled', 'disabled');
					return true;
				}
			},
			error : function(jqXHR, textStatus, errorThrown) {
				//console.log("errors");
			},
			success : function(data) {
				if(data.success.isSuccess) {
					var $out = $('#uploadOutput');
					var $img = $('<img></img').attr('src', data.data.imageLocation);
					if(data.data.imageHeight > biggestHeight){
						$('#images .image').css('min-height', data.data.imageHeight);	
					}
					
					$('#images div:first-child').children().remove();
					$('#images div:first-child').prepend($img);
				} else {
					if(data.isTimeout) {
						window.location = '/users/login';
					} else {
						var errorMessage = data.errors[0].file;
						$('#upload-error').children('span.error-message').text(errorMessage);
						$('#images div:first-child').remove();
					}
				}
			},
			complete : function() {
				$('#upload-add-button').removeAttr('disabled');
			}
		});
	});

</script>