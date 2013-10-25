<div id="user-upload-component" class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $username . '\'s' .__(' upload', true)
			?></h2>
			<div class="actions">
				<ul></ul>
			</div>
		</div>
		<?php echo $this -> element('flash'); ?>
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
			var $inputWrapper = $('<div></div>').addClass('metadata-update').addClass('spacer').addClass('clearfix');
			var type = $(this).parent('.metadata').attr('data-type');
			var length = '50';
			if (type === 'description') {
				length = '150';
			}
			var $input = $('<input></input>').addClass('form-control').attr('type', 'input').attr('maxlength', length);
			if (!$(this).parent('div.metadata').hasClass('empty')) {
				$input.val($(this).text());
			}
			var $addButton = $('<button></button>').addClass('btn').addClass('btn-primary').addClass('add-button').text('Submit');
			var $cancelButton = $('<button></button>').addClass('btn').addClass('cancel-button').addClass('btn-default').text('Cancel');

			var $buttonGroup = $('<span class="input-group-btn"></span>').append($addButton).append($cancelButton);

			var $inputGroup = $('<div class="input-group col-lg-6"></div>').append($input).append($buttonGroup);

			$inputWrapper.append($inputGroup);
			$(this).parent().append($inputWrapper);
		});
		$(document).on('click', '#user-upload-component .inside .component-view .image .metadata .metadata-update .cancel-button', function(event) {
			var $title = $(this).parent().parent().parent().parent();
			$(this).parent().parent().parent().remove();
			$title.children('a.link').show();
		});
		$(document).on('click', '#user-upload-component .inside .component-view .image .metadata .metadata-update .add-button', function(event) {
			/*
			 * For this one, I think I will submit in the background and not have it be
			 * active.  If it fails, it fails for now. Or if it fails, post some sort of top level
			 * error message
			 */
			var $eventTag = $(this);
			var $eventInput = $(this).closest('.metadata-update').find('input');
			var $eventLink = $(this).closest('.metadata-update').closest('.metadata').find('a.link');
			var type = $(this).closest('.metadata-update').closest('.metadata').attr('data-type');
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
					if (data.success.isSuccess) {
						$eventLink.text($eventInput.val());
						$eventTag.closest('.metadata-update').remove();
						$eventLink.show();
						$eventTag.closest('.metadata-update').closest('.metadata').removeClass('empty');
					} else {
						if (data.isTimeOut) {
						//	window.location = '/users/login';
						} else {
							var errorMessage = data.errors[0][type];
							$eventInput.after($('<span>' + errorMessage + '</span>').addClass('error-message'));
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