
<?php echo $this -> element('admin_actions'); ?>
<div class="col-md-10">
	<div class="title">
		<h2><?php echo __('Pending Collectibles'); ?></h2>
	</div>
	<?php echo $this -> element('flash'); ?>
	<div class="collectibles view old-school">
		<?php
		foreach ($collectibles as $collectible):
		?>
		<div class="collectible item">
			<?php echo $this -> element('collectible_list_image', array('collectible' => $collectible)); ?>
			<?php echo $this -> element('collectible_list_detail', array('collectible' => $collectible['Collectible'], 'manufacture' => $collectible['Manufacture'], 'license' => $collectible['License'], 'collectibletype' => $collectible['Collectibletype'])); ?>
			<div class="links">

			</div>
			<div class="collectible actions">
				<?php echo $this -> Html -> link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'])); ?>
			</div>
		</div>
		<?php endforeach; ?>
		<div class="paging">
			<p>
				<?php
				echo $this -> Paginator -> counter(array('format' => __('Page {:page} of {:pages}, showing {:current} collectibles out of  {:count} total.', true)));
				?>
			</p>
			<?php echo $this -> Paginator -> prev('<< ' . __('previous', true), array(), null, array('class' => 'disabled')); ?>
			<?php echo $this -> Paginator -> numbers(array('separator' => false)); ?>
			<?php echo $this -> Paginator -> next(__('next', true) . ' >>', array(), null, array('class' => 'disabled')); ?>
		</div>

	</div>
</div>

