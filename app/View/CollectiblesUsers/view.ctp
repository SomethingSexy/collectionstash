<?php echo $this -> Html -> script('views/view.stash.remove', array('inline' => false)); ?>
<?php echo $this -> Html -> script('models/model.collectible.user', array('inline' => false)); ?>
<?php echo $this -> Html -> script('cs.stash', array('inline' => false)); ?>
<div class="row stashable">
	<div class="span12 well">
		<div class="page-header">
			<h1><?php echo $stashUsername . '\'s' .__(' collectible', true)?></h1>
		</div>
	    <?php echo $this -> element('flash'); ?>	
		<div class="row">
			<div class="span12">
				<div class="actions btn-group pull-right">
					<a class="btn" href="/collectibles/view/<?php echo $collectible['Collectible']['id']; ?>" title="<?php echo __('Details'); ?>"><i class="icon-search"></i></a>
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
    	<div class="span4"  data-toggle="modal-gallery" data-target="#modal-gallery">
   
	<ul class="thumbnails">
		<li class="span4">
			<?php
			if (!empty($collectible['Collectible']['CollectiblesUpload'])) {
				foreach ($collectible['Collectible']['CollectiblesUpload'] as $key => $upload) {
					if ($upload['primary']) {
						echo '<a class="thumbnail" href="' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => true, 'width' => 1280, 'height' => 1024)) . '">' . $this -> FileUpload -> image($upload['Upload']['name'], array('imagePathOnly' => false)) . '</a>';
					}
				}
			} else {
				echo '<img src="/img/silhouette_thumb.png"/>';
			}
			?>
		</li>
	</ul>
    	

				
 
    	</div>
    	
    	<div class="span8">
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
			</dl>
   		
    	</div>	
    	
    </div>	
	</div>
	
</div>
    




    
