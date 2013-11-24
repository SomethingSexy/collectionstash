<a href="<?php echo Configure::read('Settings.domain') . '/collectibles/view/'. $Collectible['id'] ?>"><?php echo $Collectible['displayTitle'] ?></a>
<ul>
	<?php
	if (!empty($CollectiblesUser['edition_size'])) {
		echo '<li>Edition Size of' . $CollectiblesUser['edition_size'] . '</li>';
	}
	?>
	<?php
	if (!empty($CollectiblesUser['cost'])) {
		echo '<li>Cost of $' . $CollectiblesUser['cost'] . '</li>';
	}
	?>
	<?php
	if (!empty($CollectiblesUser['condition_id'])) {
		echo '<li>' . $Condition['name'] . '</li>';
	}
	?>
	<?php
	if (!empty($CollectiblesUser['merchant_id'])) {
		echo '<li>Purchased from ' . $Merchant['name'] . '</li>';
	}
	?>
	<?php
	if (!empty($CollectiblesUser['purchase_date'])) {
		echo '<li>Purchased on ' . $CollectiblesUser['purchase_date'] . '</li>';
	}
	?>
	<ul>
