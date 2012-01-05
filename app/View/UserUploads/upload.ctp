<div id="user-upload-component" class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $username . '\'s' .__(' upload', true)
			?></h2>
			<div class="actions">
				<ul></ul>
			</div>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-view">
			<?php
			echo '<div class="image" data-name="' . $userUpload['UserUpload']['name'] . '" data-id="' . $userUpload['UserUpload']['id'] . '">';
			echo '<div class="image-container">';
			echo $this -> FileUpload -> image($userUpload['UserUpload']['name'], array('width' => 500, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $userUpload['UserUpload']['user_id']));
			echo '</div>';
			if (empty($userUpload['UserUpload']['title'])) {
				echo '<div data-type="title" class="metadata title empty">';
				echo '<a class="link">Click here to add title.</a>';
				echo '</div>';
			} else {
				echo '<div data-type="title" class="metadata title">';
				echo '<a class="link">' . $userUpload['UserUpload']['title'] . '</a>';
				echo '</div>';
			}
			if (empty($userUpload['UserUpload']['description'])) {
				echo '<div data-type="description" class="metadata description empty">';
				echo '<a class="link">Click here to add description.</a>';
				echo '</div>';
			} else {
				echo '<div data-type="description" class="metadata description">';
				echo '<a class="link">' . $userUpload['UserUpload']['description'] . '</a>';
				echo '</div>';
			}

			echo '</div>';
			?>
		</div>
	</div>
</div>
<script>
	$(function() {
		var biggestHeight = 0;
		$('#user-upload-component .inside .component-view .image .metadata a').on('click', function(event) {
			$(this).hide();
			var $inputWrapper = $('<div></div>').addClass('metadata-update');
			var type = $(this).parent('.metadata').attr('data-type');
			var length = '50';
			if(type === 'description'){
				length = '150';
			}
			var $input = $('<input></input>').addClass('metadata-input').attr('type', 'input').attr('maxlength', length);
			if(!$(this).parent('div.metadata').hasClass('empty')){
				$input.val($(this).text());
			}
			var $addButton = $('<input></input>').addClass('add-button').attr('type', 'button').val('Submit');
			var $cancelButton = $('<input></input>').addClass('cancel-button').attr('type', 'button').val('Cancel');
			$inputWrapper.append($input).append($addButton).append($cancelButton);
			$(this).parent().append($inputWrapper);
		});
		$(document).on('click', '#user-upload-component .inside .component-view .image .metadata .metadata-update .cancel-button', function(event) {
			var $title = $(this).parent().parent();
			$(this).parent().remove();
			$title.children('a.link').show();
		});
		$(document).on('click', '#user-upload-component .inside .component-view .image .metadata .metadata-update .add-button', function(event) {
			/*
			 * For this one, I think I will submit in the background and not have it be
			 * active.  If it fails, it fails for now. Or if it fails, post some sort of top level
			 * error message
			 */
			var $eventTag = $(this);
			var $eventInput = $(this).parent('.metadata-update').children('.metadata-input');
			var $eventLink = $(this).parent('.metadata-update').parent('.metadata').children('a.link');
			var type = $(this).parent('.metadata-update').parent('.metadata').attr('data-type');
			var requestData = 'data[UserUpload][name]=' + $('.image', '#user-upload-component .inside .component-view').attr('data-name');
			requestData += '&data[UserUpload][data]=' + $eventInput.val();
			requestData += '&data[UserUpload][type]=' + type;
			
			$.ajax({
				url : '/user_uploads/update.json',
				data : requestData,
				dataType : 'json',
				type : 'POST',
				beforeSend : function(xhr) {

				},
				success : function(data) {
					if(data.success.isSuccess) {
						$eventLink.text($eventInput.val());
						$eventTag.parent('.metadata-update').remove();
						$eventLink.show();
						$eventTag.parent('.metadata-update').parent('.metadata').removeClass('empty');
					} else {
						if(data.isTimeout) {
							window.location = '/users/login';
						} else {
							var errorMessage = data.errors[0][type];
							$eventInput.after($('<span></span>').addClass('error-message').text(errorMessage));
						}
					}
				},
				error : function(jqXHR, textStatus, errorThrown) {

				},
				complete : function(jqXHR, textStatus) {

				}
			});
		});
	});

</script>