<div class="component" id="collectible-detail">
	<div class="inside">
		<div class="component-title">
			<h2><?php echo $stashUsername . '\'s' .__(' collectible', true)
			?></h2>
		</div>
		<?php echo $this -> element('flash');?>
		<div class="component-view">
			<div class="collectible links">
				<?php echo $html -> link('Detail', array('admin' => false, 'controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id']));?>
				<?php if(isset($isLoggedIn) && $isLoggedIn && $viewMyCollectible) { ?>
					<?php echo $html -> link('Edit', array('admin' => false, 'controller' => 'collectibles_user', 'action' => 'edit', $collectible['CollectiblesUser']['id']));?>
					<a class="link" id="remove-link"><?php echo __('Remove');?></a>
					<form id="remove-form" action="/collectibles_user/remove/<?php echo $collectible['CollectiblesUser']['id']; ?>" method="post"></form>
			  	<?php } ?>
			</div>
			<div class="collectible item">
				<div class="collectible image">
					<?php
					if (!empty($collectible['Collectible']['Upload'])) {
					?>
					<?php echo $fileUpload -> image($collectible['Collectible']['Upload'][0]['name'], array('width' => '150', 'height' => '150'));?>
					<div class="collectible image-fullsize hidden">
						<?php echo $fileUpload -> image($collectible['Collectible']['Upload'][0]['name'], array('width' => 0));?>
					</div>
					<?php } else {?><img src="/img/silhouette_thumb.png"/>
					<?php }?>
				</div>
				<div class="collectible detail">
					<dl>
						<dt>
							<?php __('Date Added');?>
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
							<?php __('Edition Size');?>
						</dt>
						<dd>
							<?php echo $collectible['CollectiblesUser']['edition_size'] . '/' . $collectible['Collectible']['edition_size'];?>
						</dd>
						<?php }?>
						<dt>
							<?php __('Purchase Price');?>
						</dt>
						<dd>
							<?php echo '$' . $collectible['CollectiblesUser']['cost'];?>
						</dd>
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
$(function(){
	$( "#remove-dialog" ).dialog({
		'autoOpen' : false,
		'width' : 500,
		'height': 'auto',
		'resizable': false,
		'modal': true,
		'buttons': {
			"Remove": function() {
				$('#remove-form').submit();
			}
		}
	});		
	$('#remove-link').click(function(){
		$('#remove-dialog').dialog('open');
	});
	
});

</script>
<div id="remove-dialog" class="dialog" title="Remove Collectible">
	<div class="component component-dialog">
		<div class="inside" >
		<div class="component-info">
			<div>
				<?php __('Are you sure you want to remove this collectible from your stash?') ?>
			</div>
		</div>
		</div>
	</div>
</div>
