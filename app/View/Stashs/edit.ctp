<div id="my-stashes-component" class="well edit-stash">
		<h2><?php echo __('Stash Edit', true)
		?></h2>
		<?php echo $this -> element('flash'); ?>
		
			<p>
				<?php echo __('Welcome to the Stash edit page!  Here you can edit the collectibles in your stash or edit the stash as a whole.  While in Edit Mode, you can select to edit individual collectibles by clicking on the link.  If you want to sort your collectibles, you can use the Sort Mode.  To sort your collectibles in the order you want, click on Sort Mode.  Then you can drag and drop the collectibles in the order you wish.  Once you have your stash looking just how you want, click save!'); ?>
			</p>
	
		
		<div class="links">
			<ul>
				<li class="mode">
					<a class="sort-mode link"><?php echo __('Sort Mode'); ?></a> |
				</li>
				<li class="mode selected">
					<a class="edit-mode link"><?php echo __('Edit Mode'); ?></a>
				</li>
				<li class="mode-button">
				   <button id="submit-sort"><?php echo __('Save'); ?> </button>
				</li>
			</ul>
		</div>
        <div id="edit-error-message" class="component-message error hidden">
            <span><div class="message"></div></span>
        </div>
		<?php
		if (isset($collectibles) && !empty($collectibles)) {
			echo '<div id="tiles" data-username="' . $stashUsername . '">';

			echo '<div class="glimpse edit-mode">';
			foreach ($collectibles as $key => $myCollectible) {
				echo '<div class="image" data-id="' . $myCollectible['CollectiblesUser']['id'] . '">';
				echo '<div class="image-container">';
				if (!empty($myCollectible['Collectible']['CollectiblesUpload'])) {
					foreach ($myCollectible['Collectible']['CollectiblesUpload'] as $key => $upload) {
						if ($upload['primary']) {
							echo '<a data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false, 'width' => 150, 'height' => 150)) . '</a>';
							break;
						}
					}
				} else {
					echo '<img src="/img/silhouette_thumb.png"/>';
				}
				echo '</div>';
				echo '<div class="actions">';
				echo '<a class="link" href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '">View</a> | ';
				echo '<a class="link" href="/collectibles_users/edit/' . $myCollectible['CollectiblesUser']['id'] . '">Edit</a> | ';
				echo '<a class="link delete">Delete</a>';
				echo '<form class="remove-form" action="/collectibles_users/remove/' . $myCollectible['CollectiblesUser']['id'] . '" method="post"></form>';
				echo '</div>';
				echo '</div>';
			}
			echo '</div>';
			echo '</div>';
		} else {
			echo '<p class="">' . __('You have no colletibles in your stash!', true) . '</p>';
		}
		?>


</div>
<?php
echo $this -> Form -> create('Stash', array('url' => '/stashs/edit'));
echo $this -> Form -> end();
?>
<script>
	$(function() {
		$('a.sort-mode').click(function() {
			$(this).parent('li').parent('ul').children('li.mode').removeClass('selected');
			$(this).parent('li').parent('ul').children('li.mode-button').children().show();
			$(this).parent('li').addClass('selected');
			$('.glimpse').removeClass('edit-mode').addClass('sort-mode');
			$(".glimpse").sortable({
				disabled : false,
				revert : true
			});

		});
		$('a.edit-mode').click(function() {
			$(this).parent('li').parent('ul').children('li.mode').removeClass('selected');
			$(this).parent('li').parent('ul').children('li.mode-button').children().hide();
			$(this).parent('li').addClass('selected');
			$('.glimpse').removeClass('sort-mode').addClass('edit-mode');
			$(".glimpse").sortable({
				disabled : true
			});
		})

		$('#submit-sort').click(function() {
			$($('.glimpse').children('div.image').get().reverse()).each(function(index, element) {

				//$('.glimpse').children('div.image').each(function(index, element) {
				var $inputId = $('<input type="hidden"/>').attr('name', 'data[CollectiblesUser][' + index + '][id]').val($(element).attr('data-id'));
				var $inputSort = $('<input type="hidden"/>').attr('name', 'data[CollectiblesUser][' + index + '][sort_number]').val(index);

				$('#StashEditForm').append($inputId);
				$('#StashEditForm').append($inputSort);

			});

			$('#StashEditForm').submit();
		});

		$(document).on('click', '#tiles .glimpse.edit-mode .image .actions a.delete', function(event) {
			var $image = $(this).parent('div.actions').parent('div.image');
			var $img = $image.children('div.image-container');
			var $actions = $image.children('div.actions');
			var imageName = $(this).parent('div.actions').parent('div.image').attr('data-id');
			var requestData = 'data[CollectiblesUsers][id]=' + imageName;
			$.ajax({
				url : '/collectibles_users/remove.json',
				data : requestData,
				dataType : 'json',
				type : 'POST',
				beforeSend : function(xhr) {
					var $loaderImg = $('<img class="loader-image"></img').attr('src', '/img/ajax-loader-circle.gif');
					$img.hide();
					$actions.hide();
					$image.append($loaderImg);
					$('#edit-error-message').hide();
				},
				success : function(data) {
					if (data.success.isSuccess) {
						//This saves me a DB call
						$('#user-count').text(parseInt($('#user-count').text()) - 1);
						$image.remove();
					} else {
						if (data.isTimeout) {
							window.location = '/users/login';
						} else {
							if (data.error.message) {
								$('#edit-error-message').children('span').children('div').text(data.error.message);
								$('#edit-error-message').show();
							}

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
	});

</script>