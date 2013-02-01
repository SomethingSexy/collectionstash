<?php      
	echo $this->element('collectible_detail', array(
		'title' => __('Collectible Details', true),
		'showStatistics' => false,
		'showWho' => false,
		'showEdit' => true,
		'editImageUrl'=> '/upload_edits/edit/',
		'editManufactureUrl' => '/collectible_edits/edit/',
		'showHistory' => false,
		'showVariants' => false,
		'setPageTitle' => true,
		'showAddedBy' => true,
		'showAddedDate' => true,
		'collectibleDetail' => $collectible,
		'showAddStash' => false,
		'adminMode' => true,
		'showTags' => true
	));		
?>
