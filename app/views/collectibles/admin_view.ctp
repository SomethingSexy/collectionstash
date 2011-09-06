<?php      
	echo $this->element('collectible_detail', array(
		'title' => __('Collectible Details', true),
		'showStatistics' => false,
		'showWho' => false,
		'showEdit' => true,
		'editImageUrl'=> '/uploadEdit/edit/',
		'editManufactureUrl' => '/collectibleEdit/manufacture/',
		'showHistory' => false,
		'showVariants' => false,
		'setPageTitle' => true,
		'showAddedBy' => true,
		'showAddedDate' => true,
		'collectibleDetail' => $collectible,
		'showAddStash' => false,
		'adminMode' => true
	));		
?>
