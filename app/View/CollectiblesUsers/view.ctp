<div class="component" id="collectible-detail">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $stashUsername . '\'s' .__(' collectible', true)
			?></h2>
			<div class="actions icon">
				<ul>
					<li>
						<a class="link" href="/collectibles/view/<?php echo $collectible['Collectible']['id']; ?>" title="<?php echo __('Details'); ?>"><img src="/img/icon/magnify.png"/></a>
					</li>
					<?php if(isset($isLoggedIn) && $isLoggedIn && $viewMyCollectible) {
					?>
					<li>
						<a class="link" href="/collectibles_users/edit/<?php echo $collectible['CollectiblesUser']['id']; ?>" title="<?php echo __('Edit'); ?>"><img src="/img/icon/pencil.png"/></a>
					</li>
					<li>
						<a class="link" title="<?php echo __('Remove'); ?>" id="remove-link"><img src="/img/icon/trash.png"/></a>
						<form id="remove-form" action="/collectibles_users/remove/<?php echo $collectible['CollectiblesUser']['id']; ?>" method="post"></form>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="component-view">

			<div class="collectible item">
				<div class="collectible image">
					<?php
if (!empty($collectible['Collectible']['Upload'])) {
					?>
					<?php echo $this -> FileUpload -> image($collectible['Collectible']['Upload'][0]['name'], array('width' => '150', 'height' => '150')); ?>
					<div class="collectible image-fullsize hidden">
						<?php echo $this -> FileUpload -> image($collectible['Collectible']['Upload'][0]['name'], array('width' => 0)); ?>
					</div>
					<?php } else { ?><img src="/img/silhouette_thumb.png"/>
					<?php } ?>
				</div>
				<div class="collectible detail">
					<dl>
						<dt>
							<?php echo __('Date Added'); ?>
						</dt>
						<dd>
							<?php
							$datetime = strtotime($collectible['CollectiblesUser']['created']);
							$mysqldate = date("m/d/y g:i A", $datetime);
							echo $mysqldate;
							?>
						</dd>
						<?php
$editionSize = $collectible['Collectible']['edition_size'];
if($collectible['Collectible']['showUserEditionSize'] && isset($collectible['CollectiblesUser']['edition_size']) && !empty($collectible['CollectiblesUser']['edition_size']))
{
						?>

						<dt>
							<?php echo __('Edition Number'); ?>
						</dt>
						<dd>
							<?php echo $collectible['CollectiblesUser']['edition_size'] . '/' . $collectible['Collectible']['edition_size']; ?>
						</dd>
						<?php } ?>
						<?php
						if (isset($collectible['CollectiblesUser']['artist_proof'])) {
							echo '<dt>';
							echo __('Artist\'s Proof');
							echo '</dt>';
							echo '<dd>';
							if ($collectible['CollectiblesUser']['artist_proof']) {
								echo __('Yes');
							} else {
								echo __('No');
							}
							echo '</dd>';
						}
						?>
						<?php
						if (isset($collectible['CollectiblesUser']['cost']) && !empty($collectible['CollectiblesUser']['cost'])) {
							echo '<dt>';
							echo __('Purchase Price');
							echo '</dt>';
							echo '<dd>';
							echo '$' . $collectible['CollectiblesUser']['cost'];
							echo '</dd>';
						}
						?>
						<?php
						if (isset($collectible['Condition']) && !empty($collectible['Condition'])) {
							echo '<dt>';
							echo __('Condition');
							echo '</dt>';
							echo '<dd>';
							echo $collectible['Condition']['name'];
							echo '</dd>';
						}
						?>
						<?php
						if (isset($collectible['Merchant']) && !empty($collectible['Merchant'])) {
							echo '<dt>';
							echo __('Purchased From');
							echo '</dt>';
							echo '<dd>';
							echo $collectible['Merchant']['name'];
							echo '</dd>';
						}
						?>
						<?php
						if (isset($collectible['CollectiblesUser']['purchase_date']) && !empty($collectible['CollectiblesUser']['purchase_date'])) {
							echo '<dt>';
							echo __('Date Purchased');
							echo '</dt>';
							echo '<dd>';
							echo $collectible['CollectiblesUser']['purchase_date'];
							echo '</dd>';
						}
						?>
					</dl>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(function() {
		$("#remove-dialog").dialog({
			'autoOpen' : false,
			'width' : 500,
			'height' : 'auto',
			'resizable' : false,
			'modal' : true,
			'buttons' : {
				"Remove" : function() {
					$('#remove-form').submit();
				}
			}
		});
		$('#remove-link').click(function() {
			$('#remove-dialog').dialog('open');
		});

	});

</script>
<div id="remove-dialog" class="dialog" title="Remove Collectible">
	<div class="component component-dialog">
		<div class="inside" >
			<div class="component-info">
				<div>
					<?php echo __('Are you sure you want to remove this collectible from your stash?')
					?>
				</div>
			</div>
		</div>
	</div>
</div>
