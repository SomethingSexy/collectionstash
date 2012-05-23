<?php      
	if(isset($isLoggedIn) && $isLoggedIn) {
		echo $this->element('collectible_detail', array(
			'title' => $collectible['Collectible']['name'],
			'showStatistics' => true,
			'showWho' => true,
			'showEdit' => true,
			'editImageUrl'=> '/upload_edits/edit/',
			'editManufactureUrl' => '/collectible_edits/edit/',
			'showHistory' => true,
			'showVariants' => true,
			'setPageTitle' => true,
			'showAddedBy' => true,
			'showAddedDate' => true,
			'collectibleDetail' => $collectible,
			'showAddStash' => true,
			'showQuickAdd' => true,
			'showTags' => true,
			'showComments' => true
		));		
		
	} else {
		echo $this->element('collectible_detail', array(
			'title' => $collectible['Collectible']['name'],
			'showStatistics' => false,
			'showWho' => false,
			'showEdit' => false,
			'showVariants' => true,
			'setPageTitle' => true,
			'showAddedBy' => true,
			'showAddedDate' => true,
			'collectibleDetail' => $collectible,
			'showQuickAdd' => false,
			'showTags' => true,
			'showComments' => true
		));			
	}
?>
