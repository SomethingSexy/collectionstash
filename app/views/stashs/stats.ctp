<div class="component" id="stash-stats">
	<div class="inside">
		<div class="component-title">
			<h2>
			<?php __('Your Stash Stats');?>
			</h2>
		</div>
		<div class="component-view">
			<div class="collectible statistics">
				<dl>
					<dt>
						<?php __('Total in Stash: ');?>
					</dt>
					<dd>
						<?php echo $stashStats['StashStats']['count'];?>
					</dd>
					<dt>
						<?php __('Total Cost of Stash: ');?>
					</dt>
					<dd>
						<?php echo $stashStats['StashStats']['cost_total'];?>
					</dd>
				</dl>
			</div>
		</div>
	</div>
</div>
