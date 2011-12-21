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
				<div class="stats">
					<span class="count"><?php echo __('Images') . ': <span id="user-count">' . $uploadCount . '</span>/' . Configure::read('Settings.User.uploads.total-allowed');?></span>
				</div>
				<form enctype="multipart/form-data" method="POST" action="/user_uploads/add.json" id="uploadForm" encoding="multipart/form-data">
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
					echo '<div class="image" data-name="' . $value['UserUpload']['name'] . '" data-id="' . $value['UserUpload']['id'] . '">';
					echo '<div class="image-container">';
					echo '<a href="/user_uploads/upload/' . $value['UserUpload']['name'] . '">' . $fileUpload -> image($value['UserUpload']['name'], array('width' => 100, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $value['UserUpload']['user_id'])) . '</a>';
					echo '</div>';
					// echo '<div class="metadata">';
					// echo $value['UserUpload']['title'];
					// echo '</div>';
					echo '<div class="actions">';
					echo '<a class="link" href="/user_uploads/upload/' . $value['UserUpload']['name'] . '">Edit</a> | ';
					echo '<a class="link delete">Delete</a>';
					echo '</div>';
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
		$(document).on('click', '#images .image .actions a.delete', function(event) {
			var $image = $(this).parent('div.actions').parent('div.image');
			var $img = $image.children('div.image-container');
			var $actions = $image.children('div.actions');
			var imageName = $(this).parent('div.actions').parent('div.image').attr('data-name');
			var requestData = 'data[UserUpload][name]=' + imageName;
			$.ajax({
				url : '/user_uploads/delete.json',
				data : requestData,
				dataType : 'json',
				type : 'POST',
				beforeSend : function(xhr) {
					var $loaderImg = $('<img class="loader-image"></img').attr('src', '/img/ajax-loader-circle.gif');
					$img.hide();
					$actions.hide();
					$image.append($loaderImg);
				},
				success : function(data) {
					if(data.success.isSuccess) {
						//This saves me a DB call
						$('#user-count').text(parseInt($('#user-count').text()) - 1);
						$image.remove();
					} else {
						if(data.isTimeout) {
							window.location = '/users/login';
						} else {
							$image.children('.loader-image').remove();
							$image.children().show();
						}
					}
				},
				error : function(jqXHR, textStatus, errorThrown) {

				},
				complete : function(jqXHR, textStatus) {

				}
			});
		});
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
					var $imageContainer = $('<div></div>').addClass('image-container');
					$imageContainer.append($img);
					if(data.data.imageHeight > biggestHeight) {
						$('#images .image').css('min-height', data.data.imageHeight);
					}
					//build actions
					var $actions = $('<div></div>').addClass('actions');
					var $edit = $('<a></a>').addClass('link').attr('href', '/user_uploads/upload/' + data.data.imageName).text('Edit');
					var $delete = $('<a></a>').addClass('link delete').text('Delete');
					$actions.append($edit).append(' | ').append($delete);
					$('#images div.image:first-child').children().remove();
					$('#images div.image:first-child').removeClass('image-loader');
					$('#images div.image:first-child').attr('data-name', data.data.imageName);
					$('#images div.image:first-child').prepend($imageContainer);
					$('#images div.image:first-child').append($actions);
					//using id cause I am lazy and it is faster
					$('#user-count').text(data.data.count);
				} else {
					if(data.isTimeout) {
						window.location = '/users/login';
					} else {
						$('#images div.image:first-child').remove();
						if(data.errors[0]['totalAllowed']) {
							$('#upload-error').children('span.error-message').text(data.errors[0]['totalAllowed']);
						} else if(data.errors[0]['file']) {
							$('#upload-error').children('span.error-message').text(data.errors[0]['file']);
						} else {
							$('#upload-error').children('span.error-message').text('Sorry, there was a problem with your upload.');
						}
					}
				}
			},
			complete : function() {
				$('#upload-add-button').removeAttr('disabled');
			}
		});
	});

</script>