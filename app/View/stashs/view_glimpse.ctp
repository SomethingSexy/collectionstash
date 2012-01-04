<div id="my-stashes-component" class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $stashUsername . '\'s' .__(' stash', true)
			?></h2>
			<div class="actions">
				<ul>
					<?php
					if (isset($myStash) && $myStash) {
						echo '<li><a class="link add-stash-link" href="/collectibles/search"><img src="/img/icon/add_stash_link.png"/></a></li>';
						if (Configure::read('Settings.User.uploads.allowed')) {
							echo '<li><a class="link upload-link" href="/user_uploads/uploads"><img src="/img/icon/upload_link.png"/></a></li>';
						}
					}
					?>
					<li>
						<?php echo '<a class="link detail-link" href="/stashs/view/' . $stashUsername . '/view:detail"><img src="/img/icon/detail_link.png"/></a>';?>
					</li>
					<li>
						<?php echo '<a class="link glimpse-link" href="/stashs/view/' . $stashUsername . '/view:glimpse"><img src="/img/icon/glimpse_link.png"/></a>';?>
					</li>
				</ul>
			</div>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-view">
			<div id="gallery"></div>
			<?php
			if (isset($collectibles) && !empty($collectibles)) {
				echo '<div class="glimpse">';
				$count = 0;
				$collectiblesCount = count($collectibles) - 1;
				foreach ($collectibles as $key => $myCollectible) {
					$newline = false;
					$endline = false;
					//First chec to see if we are starting over
					if ($count === 0) {
						$newline = true;
						$count += $count + 1;
					} else {
						//If not first, check to see if we are on the 5th one or not
						if ($count % 4 != 0) {
							//if we are not, lets increase
							$count += $count + 1;
						} else {
							//if we are, then lets start over and make sure we close out the div.
							$count = 0;
							$endline = true;
						}
					}

					if (!$endline) {
						if (($collectiblesCount) === $key) {
							$endline = true;
						}
					}

					if ($newline) {
						echo '<div class="line">';
					}
					if (!empty($myCollectible['Collectible']['Upload'])) {
						echo '<div class="image">';
						echo '<a href="/collectibles_user/view/' . $myCollectible['CollectiblesUser']['id'] . '">' . $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array('uploadDir' => 'files', 'width' => 150, 'height' => 150)) . '</a>';
						echo '</div>';
						//echo $fileUpload -> image($myCollectible['Collectible']['Upload'][0]['name'], array());
					} else {
						echo '<div class="image"><a href="/collectibles_user/view/' . $myCollectible['CollectiblesUser']['id'] . '"><img src="/img/silhouette_thumb.png"/></a></div>';
					}
					if ($endline) {
						echo '</div>';
					}
				}
				echo '<div class="links">';
				echo '<a href="/stashs/view/' . $stashUsername . '/view:detail">See more collectibles</a>';
				echo '</div>';
				echo '</div>';
			} else {
				echo '<p class="">' . $stashUsername . __(' has no collectibles in their stash!', true) . '</p>';
			}
			?>

			<div class="paging">
				<p>
					<?php
					//$this -> Paginator -> counter(array('format' => __('Page %page% of %pages%, showing %current% collectibles out of %count% total.', true)));
					?>
				</p>
				<?php //$this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));?>
				<?php //$this -> Paginator -> numbers();?>
				<?php //$this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
			</div>
		</div>
	</div>
</div>
<?php echo $this -> Html -> script('galleria-1.2.5', array('inline' => false));?>
<?php echo $this -> Html -> script('galleria.classic.js', array('inline' => false));?>
<?php echo $this -> Html -> css('galleria.classic');?>

<script>
	$(function() {

var data = [<?php
if (isset($userUploads) && !empty($userUploads)) {

	foreach ($userUploads as $key => $userUpload) {
		echo '{';
		echo 'image : "' . $fileUpload -> image($userUpload['name'], array('width' => 0, 'imagePathOnly' => true, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $userUpload['user_id'])) . '",';
		echo 'thumb : "' . $fileUpload -> image($userUpload['name'], array('imagePathOnly' => true, 'height' => 40, 'width' => 41, 'title' => $userUpload['title'], 'alt' => $userUpload['description'], 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $userUpload['user_id'])) . '",';
		echo 'title : "' . $userUpload['title'] . '",';
		echo 'description : "' . $userUpload['description'] . '"';
		echo '}';
		if ($key != (count($pages) - 1)) {
			echo ',';
		}
	}
}
?>
	];
	if(0 < data.length) {
		$("#gallery").galleria({
			width : 900,
			height : 500,
			lightbox : true,
			data_source : data
		});
	}
	});
</script>
