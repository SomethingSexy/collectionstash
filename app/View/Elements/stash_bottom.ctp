				</div>
				<div id="photos" class="ui-tabs-hide">
		
					<div id="photo-gallery"></div>
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