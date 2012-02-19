				</div>
				<div id="photos" class="ui-tabs-hide">
					<div class="actions icon">
						<ul>
							<?php
							if (isset($myStash) && $myStash) {
								if (Configure::read('Settings.User.uploads.allowed')) {
									echo '<li><a title="Upload Photos" class="link upload-link" href="/user_uploads/uploads"><img src="/img/icon/upload_photo.png"/></a></li>';
								}
							}
							?>
						</ul>
					</div>
					<div id="photo-gallery"></div>
				</div>
				<div id="tabs-3" class="ui-tabs-hide"></div>
			</div>
		</div>
	</div>
</div>
<?php echo $this -> Html -> script('galleria-1.2.6', array('inline' => false));?>
<?php echo $this -> Html -> script('galleria.classic.js', array('inline' => false));?>
<?php echo $this -> Html -> css('galleria.classic');?>

<script>
	var photoData = [<?php
if (isset($userUploads) && !empty($userUploads)) {

	foreach ($userUploads as $key => $userUpload) {
		echo '{';
		echo 'image : "' . $this -> FileUpload -> image($userUpload['name'], array('width' => 0, 'imagePathOnly' => true, 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $userUpload['user_id'])) . '",';
		echo 'thumb : "' . $this -> FileUpload -> image($userUpload['name'], array('imagePathOnly' => true, 'height' => 100, 'width' => 100, 'title' => $userUpload['title'], 'alt' => $userUpload['description'], 'uploadDir' => Configure::read('Settings.User.uploads.root-folder') . '/' . $userUpload['user_id'])) . '",';
		echo 'title : "' . $userUpload['title'] . '",';
		echo 'description : "' . $userUpload['description'] . '"';
		echo '}';
		if ($key != (count($userUploads) - 1)) {
			echo ',';
		}
	}
}
?>
	];	
	
</script>