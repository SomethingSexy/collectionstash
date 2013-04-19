<div class="component">
	<div class="inside" >
		<div class="component-title">
			<h2><?php echo __('Add to Stash')
			?></h2>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="component-info">
			<div>
				<?php echo __('Tell us about your collectible.')
				?>
			</div>
		</div>
		<div class="component-view">
			<?php echo $this -> Form -> create('CollectiblesUser'); ?>
			<fieldset>
				<ul class="form-fields unstyled">
					<?php
$editionSize = $collectible['Collectible']['edition_size'];
if($collectible['Collectible']['numbered'])
{
					?>
					<li>
						<div class="label-wrapper">
							<label for="collectibleType"><?php echo __('Edition Number')
								?> (Total: <?php echo $collectible['Collectible']['edition_size']
								?>)</label>
						</div>
						<?php  echo $this -> Form -> input('edition_size', array('div' => false, 'label' => false)); ?>
					</li>
					<?php } ?>
					<li>
						<div class="label-wrapper">
							<label for="dialogCost"><?php echo __('Artist\'s Proof')
								?></label>
						</div>
						<?php echo $this -> Form -> input('artist_proof', array('div' => false, 'label' => false)); ?>
					</li>
					<li>
						<div class="label-wrapper">
							<label for="dialogCost"><?php echo __('How much did you pay?')
								?> <?php 
								if($collectible['Collectible']['msrp']) {
									echo '(Retail:' . $collectible['Currency']['sign']; echo $collectible['Collectible']['msrp'].')';
								}
								?></label>
						</div>
						<?php echo $this -> Form -> input('cost', array('id' => 'dialogCost', 'div' => false, 'label' => false)); ?>
					</li>
					<li>
						<div class="label-wrapper">
							<label for="CollectiblesUserConditionId"><?php echo __('Condition')
								?></label>
						</div>
						<?php echo $this -> Form -> input('condition_id', array('div' => false, 'label' => false, 'empty' => true)); ?>
					</li>
					<li>
						<div class="label-wrapper">
							<label for="CollectiblesUserMerchantId"><?php echo __('Where did you purchase the collectible?')
								?></label>
						</div>
						<?php echo $this -> Form -> input('merchant', array('type' => 'text', 'div' => false, 'label' => false, 'maxLength' => 150)); ?>
					</li>
					<li>
						<div class="label-wrapper">
							<label for=""><?php echo __('When did you purchase this collectible?')
								?></label>
						</div>
						<?php echo $this -> Form -> text('purchase_date', array('div' => false, 'label' => false, 'maxLength' => 8)); ?>
					</li>
					<?php echo $this -> Form -> hidden('CollectiblesUser.collectible_id'); ?>
				</ul>
			</fieldset>
			<input type="submit" value="Add" class="btn btn-primary">
			<?php echo $this -> Form -> end(); ?>
		</div>
	</div>
</div>
	<?php
	if (isset($collectibles) && !empty($collectibles)) {?>

<div id="my-stashes-component" class="component">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo __('Just an FYI, you already have this collectible in your stash...'); ?></h2>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="component-view">
	<?php
	if (isset($collectibles) && !empty($collectibles)) {
		echo '<div class="tiles">';

		foreach ($collectibles as $key => $myCollectible) {
			echo '<div class="tile">';

			if (!empty($myCollectible['Collectible']['CollectiblesUpload'])) {
				foreach ($myCollectible['Collectible']['CollectiblesUpload'] as $key => $upload) {
					if ($upload['primary']) {
						echo '<div class="image">';
						echo '<a data-gallery="gallery" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'uploadDir' => 'files', 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false, 'uploadDir' => 'files', 'width' => 150, 'height' => 150)) . '</a>';
						echo '</div>';
						break;
					}
				}
			} else {
				echo '<div class="image"><a href="/collectibles_users/view/' . $myCollectible['CollectiblesUser']['id'] . '"><img src="/img/silhouette_thumb.png"/></a></div>';
			}

			echo '<div class="description">';
			echo '<span>' . $myCollectible['Collectible']['Collectibletype']['name'] . ' </span> <span>' . $myCollectible['Collectible']['Manufacture']['title'] . '</span>';
			echo '</div>';

			$detail = '';

			$editionSize = $myCollectible['Collectible']['edition_size'];
			if ($myCollectible['Collectible']['showUserEditionSize'] && isset($myCollectible['CollectiblesUser']['edition_size']) && !empty($myCollectible['CollectiblesUser']['edition_size'])) {
				$detail .= $myCollectible['CollectiblesUser']['edition_size'] . '/' . $myCollectible['Collectible']['edition_size'];

			} else if (isset($myCollectible['CollectiblesUser']['artist_proof'])) {
				if ($myCollectible['CollectiblesUser']['artist_proof']) {
					$detail .= __('Artist\'s Proof');
				}
			}
			$datetime = strtotime($myCollectible['CollectiblesUser']['created']);
			$mysqldate = date("m/d/y g:i A", $datetime);
			$detail .= '<div class="date">' . $mysqldate . '</div>';

			echo '<div class="user-detail">';
			echo $detail;
			echo '</div>';
			echo '</div>';
		}
		echo '</div>';
	}
?>
		</div>
	</div>
</div>
<?php } ?>

<script><?php
echo 'var merchants=[';

foreach ($merchants as $key => $value) {
	echo '\'' . addslashes($value['Merchant']['name']) . '\'';
	if ($key != (count($merchants) - 1)) {
		echo ',';
	}
}
echo '];';
?>
	$(function() {
		$("#CollectiblesUserPurchaseDate").datepicker();
		var options, a;
		jQuery(function() {
			options = {
				lookup : merchants,
				width : 282,
				onSelect : function(value, data) {
					// Not sure I need to do anything here
				}
			};
			a = $('#CollectiblesUserMerchant').autocomplete(options);
		});

	});

</script>