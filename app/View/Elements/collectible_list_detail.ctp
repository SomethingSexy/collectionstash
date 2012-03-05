<?php
if (!isset($showStatus)) {
	$showStatus = false;
}
if(!isset($showStats)) {
	$showStats = false;
}
?>

<div class="collectible detail">
	<dl>
		<dt>
			Name:
		</dt>
		<dd>
			<?php echo $collectible['name'];?><?php
			if ($collectible['exclusive']) { echo __(' - Exclusive');
			}
			?>
		</dd>
		<?php
		if ($collectible['variant']) {
			echo '<dt>';
			echo __('Variant:');
			echo '</dt><dd>';
			echo __('Yes');
			echo '</dd>';

		}
		?>
		<dt>
			Manufacturer:
		</dt>
		<dd>
			<a href="<?php echo '/manufactures/view/' . $manufacture['id'];?>"> <?php echo $manufacture['title'];?></a>
		</dd>
		<dt>
			<?php echo __('Brand'); ?>:
		</dt>
		<dd>
			<?php echo $license['name'];?>
		</dd>
		<dt>
			Type:
		</dt>
		<dd>
			<?php echo $collectibletype['name'];?>
		</dd>
		<?php
if(isset($speciazliedType)) {
		?>
		<dt>
			Manufacturer Type:
		</dt>
		<dd>
			<?php echo $speciazliedType['name'];?>
		</dd>
		<?php }?>
		<?php
		if ($showStatus) {
			echo '<dt>';
			echo __('Status');
			echo '</dt>';
			echo '<dd>';
			if ($collectible['state'] == 0) {
				echo __('Approved');
			} else {
				echo __('Pending Approval');
			}
			echo '</dd>';
		}
		?>
		<?php
		if ($showStats) {
			echo '<dt>';
			echo __('Owned By:');
			echo '</dt>';
			echo '<dd>';
			echo $collectible['collectibles_user_count'];
			echo '</dd>';
		}
		?>
	</dl>
</div>