<?php      
	if(isset($isLoggedIn) && $isLoggedIn) {
		echo $this->element('collectible_detail', array(
			'title' => __('Collectible Details', true),
			'showStatistics' => true,
			'showWho' => true,
			'showEdit' => true,
			'editImageUrl'=> '/uploadEdit/edit/',
			'showHistory' => true,
			'showVariants' => true,
			'setPageTitle' => true
		));		
		
	} else {
		echo $this->element('collectible_detail', array(
			'title' => __('Collectible Details', true),
			'showStatistics' => false,
			'showWho' => false,
			'showEdit' => false,
			'showVariants' => true,
			'setPageTitle' => true
		));			
	}
?>
