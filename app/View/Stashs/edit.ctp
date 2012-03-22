<div id="my-stashes-component" class="component edit-stash">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Stash Edit', true)
			?></h2>
			<div class="actions icon">
				<ul>
					<?php

                    echo '<li><a title="Add Collectibles" class="link add-stash-link" href="/collectibles/search"><img src="/img/icon/add_stash_link_25x25.png"/></a></li>';
					?>
					<li>
						<?php echo '<a title="Photo Gallery" class="link detail-link" href="/stashs/view/' . $stashUsername . '"><img src="/img/icon/photos.png"/></a>';?>
					</li>
				</ul>
			</div>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-info">
			<p>
				<?php echo __('Welcome to the Stash edit page!  Here you can edit the collectibles in your stash or edit the stash as a whole.  While in Edit Mode, you can select to edit individual collectibles by clicking on the link.  If you want to sort your collectibles, you can use the Sort Mode.  To sort your collectibles in the order you want, click on Sort Mode.  Then you can drag and drop the collectibles in the order you wish.  Once you have your stash looking just how you want, click save!');?>
			</p>
		</div>
		<div class="component-view">
			<div class="links">
				<ul>
					<li class="mode">
						<a class="sort-mode link"><?php echo __('Sort Mode');?></a> |
					</li>
					<li class="mode selected">
						<a class="edit-mode link"><?php echo __('Edit Mode');?></a>
					</li>
					<li class="mode-button">
					   <button id="submit-sort"><?php echo __('Save');?> </button>
					</li>
				</ul>
			</div>

			<?php
            if (isset($collectibles) && !empty($collectibles)) {
                echo '<div id="tiles" data-username="' . $stashUsername . '">';

                echo '<div class="glimpse edit-mode">';
                foreach ($collectibles as $key => $myCollectible) {
                    echo '<div class="image" id="' . $myCollectible['CollectiblesUser']['id'] . '">';
                    if (!empty($myCollectible['Collectible']['Upload'])) {
                        echo $this -> FileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('uploadDir' => 'files', 'width' => 150, 'height' => 150));
                    } else {
                        echo '<img src="/img/silhouette_thumb.png"/>';
                    }
                    echo '<div class="actions">';
                    echo '<a class="link" href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '">View</a> | ';
                    echo '<a class="link" href="/collectibles_users/edit/' . $myCollectible['CollectiblesUser']['id'] . '">Edit</a>';
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
	</div>
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
				var $inputId = $('<input type="hidden"/>').attr('name', 'data[CollectiblesUser][' + index + '][id]').val($(element).attr('id'));
				var $inputSort = $('<input type="hidden"/>').attr('name', 'data[CollectiblesUser][' + index + '][sort_number]').val(index);

				$('#StashEditForm').append($inputId);
				$('#StashEditForm').append($inputSort);

			});

			$('#StashEditForm').submit();
		});
	});

</script>