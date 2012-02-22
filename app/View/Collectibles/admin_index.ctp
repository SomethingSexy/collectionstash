<div class="two-column-page">
	<div class="inside">
		<div class="actions">
			<ul>
				<li>
					<h3><?php echo __('Admin');?></h3>
					<ul>
						<li>
							<?php echo $this -> Html -> link('New Collectibles', '/admin/collectibles/index', array('class' => 'link'));?>
						</li>
						<li>
							<?php echo $this -> Html -> link('Edits', '/admin/edits/index', array('class' => 'link'));?>
						</li>
					</ul>
				</li>
				<li>
					<h3><?php echo __('Manufacturers');?></h3>
					<ul>
						<li>
							<?php echo $this -> Html -> link('Detail', '/admin/manufactures/list', array('class' => 'link'));?>
						</li>
					</ul>
				</li>
				<li>
					<h3><?php echo __('Series');?></h3>
					<ul>
						<li>
							<?php echo $this -> Html -> link('View', '/admin/series/view', array('class' => 'link'));?>
						</li>
					</ul>
				</li>
			</ul>
		</div>
		<div class="page">
			<div class="title">
				<h2>
					<?php echo __('Pending Collectibles');?>
				</h2>				
			</div>
			<?php echo $this -> element('flash');?>
			<div class="collectibles view">
				<?php
				foreach ($collectibles as $collectible):
				?>
				<div class="collectible item">
					<?php echo $this -> element('collectible_list_image', array(
						'collectible' => $collectible
					));?>
					<?php echo $this -> element('collectible_list_detail', array(
						'collectible' => $collectible['Collectible'],
						'manufacture' => $collectible['Manufacture'],
						'license' => $collectible['License'],
						'collectibletype' => $collectible['Collectibletype']
					));?>
					<div class="links">

					</div>
					<div class="collectible actions">
						<?php echo $this -> Html -> link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id']));?>
					</div>
				</div>
				<?php endforeach;?>
				<div class="paging">
					<p>
						<?php
						echo $this -> Paginator -> counter( array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
						?>
					</p>
					<?php echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled'));?>
					<?php echo $this -> Paginator -> numbers(array('separator'=> false));?>
					<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
				</div>

			</div>		
		</div>
		
	</div>
</div>
