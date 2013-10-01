<h2><?php
	echo __('Edit Your Stash');
?></h2>
<div id="my-stashes-component" class="widget">
	<div class="widget-header">
		<h3><?php echo __('Sort Stash', true)
		?></h3>		
	</div>
	<div class="widget-content">
		<?php echo $this -> element('flash'); ?>
		<p><?php echo __('To sort your collectibles, drag and drop the collectibles in the order you wish.  Once you have your stash looking just how you want, click save!'); ?></p>
		<button class="btn btn-primary" id="submit-sort"><?php echo __('Save'); ?> </button>
        <div id="edit-error-message" class="component-message error hidden">
            <span><div class="message"></div></span>
        </div>
		<?php
		if (isset($collectibles) && !empty($collectibles)) {
			echo '<div id="tiles" data-username="' . $stashUsername . '">';

			echo '<div class="tiles sort-mode">';
			foreach ($collectibles as $key => $myCollectible) {
				echo '<div class="tile stash-item" data-id="' . $myCollectible['CollectiblesUser']['id'] . '">';
				if (!empty($myCollectible['Collectible']['CollectiblesUpload'])) {
					foreach ($myCollectible['Collectible']['CollectiblesUpload'] as $key => $upload) {
						if ($upload['primary']) {
							echo '<div class="image">';
							echo '<a data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files')) . '">';
							$this -> FileUpload -> reset();
							echo  $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false, 'uploadDir' => 'files', 'width' => 150, 'height' => 150)) . '</a>';
							echo '</div>';
							break;
						}
					}

					//echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array());
				} else {
					echo '<div class="image"><a href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '"><img src="/img/silhouette_thumb.png"/></a></div>';
				}
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
<?php
echo $this -> Form -> create('Stash', array('url' => '/stashs/edit'));
echo $this -> Form -> end();
?>
<script>
	$(function() {

		$(".tiles").sortable({
			disabled : false,
			revert : true
		});

		$('#submit-sort').click(function() {
			$($('.tiles').children('div.tile').get().reverse()).each(function(index, element) {

				//$('.glimpse').children('div.image').each(function(index, element) {
				var $inputId = $('<input type="hidden"/>').attr('name', 'data[CollectiblesUser][' + index + '][id]').val($(element).attr('data-id'));
				var $inputSort = $('<input type="hidden"/>').attr('name', 'data[CollectiblesUser][' + index + '][sort_number]').val(index);

				$('#StashEditForm').append($inputId);
				$('#StashEditForm').append($inputSort);

			});

			$('#StashEditForm').submit();
		});
	});

</script>