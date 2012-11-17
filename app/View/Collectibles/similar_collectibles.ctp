<?php echo $this -> Minify -> script('js/collectible-list', array('inline' => false));?>

<div class="component" id="collectible-add-component">
	<div class="inside" >
		<div class="component-title">
			<h2>
			<?php echo __('Submit New Collectible', true); ?>
			</h2>
		</div>
		<div class="component-info">
			<div>
				<?php echo __('Oh no!  It looks like the collectible you are trying to add might have been added already.  Please review the existing collectibles below.  If it doesn\'t exist then click the big button.') ?>
			</div>
		</div>
		<div class="component-view">
			<div class="collectibles view">

				<?php
				foreach ($similarCollectibles as $collectible):
				?>
				<div class="collectible item">
					<?php echo $this -> element('collectible_list_image', array(
						'collectible' => $collectible
					));?>
					<?php echo $this -> element('collectible_list_detail', array(
						'collectible' => $collectible['Collectible'],
						'manufacture' => $collectible['Manufacture'],
						'license' => $collectible['License'],
						'collectibletype' => $collectible['Collectibletype'],
						'showStatus' => true
					));?>
					<!-- <div class="links">
					<?php //At some point we should use Ajax to pull this data back. ?>
					<input type="hidden" class="collectibleId" value='<?php echo $collectible['Collectible']['id']; ?>' />
					<input type="hidden" class="showEditionSize" value='<?php echo $collectible['Collectible']['showUserEditionSize']; ?>' />
					<a title="Add to stash" class="ui-icon ui-icon-plus add-to-collection">Add to Stash</a>
					<?php

					// echo $html->link('Add to Stash', array('controller' => 'collections', 'action' => 'add', $collectible['Collectible']['id'],'stashId'=>$stashId));
					?>
					</div>-->
					<div class="collectible actions">
						<?php echo $this -> Html -> link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id']));?>
					</div>
				</div>
				<?php endforeach;?>
			</div>
			<div class="links">
				<?php echo $this->Form->create(null, array('url'=> $this->here));?>
					<input type="hidden" name="data[addAnyway]"	value="true" />
					<input type="submit" name="submit" value="Submit Anyway!" class="btn btn-primary"/>
				<?php echo $this->Form->end();?>
				<?php echo $this->Form->create(null, array('url'=>'/collectibles/wizard/manufacture', 'type'=>'get'));?>
				<input type="submit" name="submit" value="Edit" class="btn"/>
				<?php echo $this->Form->end();?>
				<?php echo $this->Form->create(null, array('url'=>'/collectibles/cancel'));?>
				<input type="submit" name="submit" value="Cancel" class="btn"/>
				<?php echo $this->Form->end();?>
				
			</div>			
		</div>
	</div>
</div>