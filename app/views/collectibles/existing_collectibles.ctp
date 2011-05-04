<?php echo $this -> Html -> script('collectible-list', array('inline' => false));?>

<div class="component" id="collectibles-list-component">
	<div class="inside" >
		<div class="component-title">
			<h2>
			<?php __('Add Collectible');?>
			</h2>
		</div>
		<div class="component-info">
			<div>
				<?php __('Oh no!  It looks like the collectible you are trying to add might have been added before.  Please review the existing collectibles below.  If it doesn\'t exist then click the big button.') ?>
			</div>
			<div>
				<form action="/collectibles/review" method="post">
					<input type="submit" id="submitAnyway" value="Submit Anyway!" />
				</form>
				
			</div>
		</div>
		<div class="component-view">
			<div class="collectibles view">

				<?php
				foreach ($existingCollectibles as $collectible):
				?>
				<div class="collectible item">
					<div class="collectible image">
						<?php echo $fileUpload -> image($collectible['Upload'][0]['name'], array('width' => '100'));?>
						<div class="collectible image-fullsize hidden">
							<?php echo $fileUpload -> image($collectible['Upload'][0]['name'], array('width' => 0));?>
						</div>
					</div>
					<div class="collectible detail">
						<dl>
							<dt>
								Name:
							</dt>
							<dd>
								<?php echo $collectible['Collectible']['name'];?><?php
								if($collectible['Collectible']['exclusive']) { __(' - Exclusive');
								}
 								?>
							</dd>
							<?php
							if($collectible['Collectible']['variant']) {
								echo '<dt>';
								__('Variant:');
								echo '</dt><dd>';
								__('Yes');
								echo '</dd>';

							}
							?>
							<dt>
								Manufacture:
							</dt>
							<dd>
								<a target="_blank" href="<?php echo $collectible['Manufacture']['url'];?>">
								<?php echo $collectible['Manufacture']['title'];?>
								</a>
							</dd>
							<dt>
								Type:
							</dt>
							<dd>
								<?php echo $collectible['Collectibletype']['name'];?>
							</dd>
						</dl>
					</div>
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
						<?php echo $html -> link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id']));?>
					</div>
				</div>
				<?php endforeach;?>
				<div class="paging">
					<p>
						<?php
						echo $this -> Paginator -> counter( array('format' => __('Page %page% of %pages%, showing %current% collectibles out of %count% total.', true)));
						?>
					</p>
					<?php echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));?>
					<?php echo $this -> Paginator -> numbers();?>
					<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
				</div>
			</div>
		</div>
	</div>
</div>
