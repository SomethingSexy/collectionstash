<?php echo $this -> Html -> script('views/view.stash.remove', array('inline' => false)); ?>
<?php echo $this -> Html -> script('models/model.collectible.user', array('inline' => false)); ?>
<?php echo $this -> Html -> script('cs.stash', array('inline' => false)); ?>
<div class="row stashable">
	<div class="col-md-12">
		<div class="panel panel-default">
		<div class="panel-heading">
			<h1 class="panel-title"><?php echo $stashUsername . '\'s' .__(' collectible', true)?></h1>
		</div>
		<div class="panel-body">
	    <?php echo $this -> element('flash'); ?>	
		<div class="row">
			<div class="col-md-12">
				<div class="actions btn-group pull-right">
					<a class="btn" href="/collectibles/view/<?php echo $collectible['Collectible']['id']; ?>" title="<?php echo __('Details'); ?>"><i class="icon-info"></i></a>
					<?php if(isset($isLoggedIn) && $isLoggedIn && $viewMyCollectible) {
					
						$collectibleJSON = json_encode($collectible['Collectible']);
						$collectibleJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleJSON));
			
						$collectibleUserJSON = json_encode($collectible['CollectiblesUser']);
						$collectibleUserJSON = htmlentities(str_replace(array("\'", "'"), array("\\\'", "\'"), $collectibleUserJSON));
						
						echo '<a data-remove-redirect="/stash/' . $username .'" data-prompt="true" data-stash-type="Default" data-collectible-user=\'' . $collectibleUserJSON . '\' data-collectible=\'' . $collectibleJSON . '\' data-collectible-user-id="' . $collectible['CollectiblesUser']['id'] . '" class="btn remove-from-stash" title="Remove" href="#"><i class="icon-trash icon-large"></i></a>';
					?>
					<a class="btn" href="/collectibles_users/edit/<?php echo $collectible['CollectiblesUser']['id']; ?>" title="<?php echo __('Edit'); ?>"><i class="icon-pencil icon-large"></i></a>
					<?php } ?>
				</div>				
			</div>
		</div>


    <div class="row">
    	<div class="col-md-4"  data-toggle="modal-gallery" data-target="#modal-gallery">
   
			<?php
			if (!empty($collectible['Collectible']['CollectiblesUpload'])) {
				foreach ($collectible['Collectible']['CollectiblesUpload'] as $key => $upload) {
					if ($upload['primary']) {
						echo '<a class="thumbnail col-md-12" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false)) . '</a>';
					}
				}
			} else {
				echo '<img src="/img/silhouette_thumb.png"/>';
			}
			?>	
    	</div>
    	
    	<div class="col-md-8">
			<?php
			if ($collectible['CollectiblesUser']['active']) {
				if ($collectible['CollectiblesUser']['sale']) {
					echo '<div class="alert alert-success">';
					echo '<p>' . __('This collectible is currently for ');
					if ($collectible['Listing']['listing_type_id'] === '2') {
						echo __('sale.');
					} else if ($collectible['Listing']['listing_type_id'] === '2') {
						echo __('trade.');
					}
					echo '</p></div>';
				}
			} else {
				echo '<div class="alert alert-danger">';
				echo '<p>' . __('This collectible is inactive.  It was removed on ') . $collectible['CollectiblesUser']['remove_date'] . __(' because it was ');
				if ($collectible['CollectiblesUser']['collectible_user_remove_reason_id'] === '1') {
					echo __('sold.');
				} else if ($collectible['CollectiblesUser']['collectible_user_remove_reason_id'] === '2') {
					echo __('traded.');
				}
				echo '</p></div>';
			}
			?>
			
			
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
				if (isset($collectible['Condition']) && !empty($collectible['Condition']['id'])) {
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
				<?php
				if (isset($collectible['CollectiblesUser']['notes']) && !empty($collectible['CollectiblesUser']['notes']) && (!$collectible['CollectiblesUser']['notes_private'] || $viewMyCollectible)) {
					echo '<dt>';
					echo __('Notes');
					echo '</dt>';
					echo '<dd>';

					$value = str_replace('\n', "\n", $collectible['CollectiblesUser']['notes']);
					$value = str_replace('\r', "\r", $value);
					$value = nl2br($value);
					$vaule = html_entity_decode($value);

					echo $value;
					echo '</dd>';
				}
				?>		
				<?php
				// right now this only supports type 1 and 2 remove reasons which only have one transaction
				if (isset($collectible['CollectiblesUser']['sold_cost'])) {
					echo '<dt>';
					if ($collectible['CollectiblesUser']['active']) {
						echo __('Asking Price');
					} else {
						echo __('Sold For');
					}
					echo '</dt>';
					echo '<dd>';
					echo '$' . $collectible['CollectiblesUser']['sold_cost'];
					echo '</dd>';
				} else if (isset($collectible['CollectiblesUser']['traded_for'])) {
					echo '<dt>';
					if ($collectible['CollectiblesUser']['active']) {
						echo __('Trade For');
					} else {
						echo __('Traded For');
					}
					echo '</dt>';
					echo '<dd>';
					echo $collectible['CollectiblesUser']['traded_for'];
					echo '</dd>';
				}

				if (isset($collectible['CollectiblesUser']['remove_date'])) {
					echo '<dt>';
					echo __('Remove Date');
					echo '</dt>';
					echo '<dd>';
					echo $collectible['CollectiblesUser']['remove_date'];
					echo '</dd>';
				}
				?>		
				</dl>
   		
    	</div>	
    	
    </div>	
    </div>
    		</div>
	</div>
	
</div>
    
<script><?php
if (isset($reasons)) {
	echo 'var reasons = \'' . json_encode($reasons) . '\';';
}
	?></script>



    
