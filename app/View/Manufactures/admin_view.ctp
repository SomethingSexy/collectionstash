<div class="row">
	<?php echo $this -> element('admin_actions'); ?>
	<div class="span8">
		<div class="page">
			<div class="title">
				<h2><?php echo __('Manufacturer Detail'); ?></h2>
				<div class="btn-group actions">
					<a class="btn" href="/admin/manufactures/edit/<?php echo $manufacture['Manufacture']['id']; ?>"> <i class="icon-pencil icon-large"></i> </a>
					<a class="btn show-delete-modal" href=""> <i class="icon-trash icon-large"></i> </a>
				</div>
			</div>
			<?php echo $this -> element('flash'); ?>
			<div class="manufacturer view">
				<div class="manufacturer detail">
					<dl>
						<dt>
							<?php echo __('Title'); ?>
						</dt>
						<dd>
							<?php echo $manufacture['Manufacture']['title']; ?>
						</dd>
						<?php
						if (isset($manufacture['Manufacture']['url'])) {?>
						<dt>
							<?php echo __('URL'); ?>
						</dt>
						<dd>
							<?php echo $manufacture['Manufacture']['url']; ?>
						</dd>
						<?php } ?>
						<dt>
							<?php echo __('Collectible Count'); ?>
						</dt>
						<dd>
							<?php echo $manufacture['Manufacture']['collectible_count']; ?>
						</dd>
						<?php
						if (isset($manufacture['Manufacture']['series_id'])) {?>
						<dt>
							<?php echo __('Series Id'); ?>
						</dt>
						<dd>
							<?php echo $manufacture['Manufacture']['series_id']; ?>
						</dd>
						<?php } ?>

					</dl>
				</div>
				<ul id="myTab" class="nav nav-tabs">
					<li>
						<a data-toggle="tab" href="#tabs-2"><?php echo __('Brands'); ?></a>
					</li>
					<li>
						<a data-toggle="tab" href="#tabs-3"><?php echo __('Collectible Platforms'); ?></a>
					</li>
				</ul>
				<div id="myTabContent" class="tab-content">
				<div id="tabs-2" class="tab-pane">
					<div class="licenses">
					    <div class="title">
                            <h3><?php echo __('Brands'); ?></h3>
                            <div class="btn-group actions">
                                <?php echo $this -> Html -> link('<i class="icon-pencil icon-large"></i>', array('action' => 'add_license', $manufacture['Manufacture']['id']), array("class" => 'btn', 'escape' => false)); ?> 
                            </div>						        
					    </div>
						<div class="standard-list">
							<ul>
								<?php
								foreach ($licenses as $key => $license) {
									echo '<li>';
									echo '<span class="name">';
									echo $license['License']['name'];
									echo '</span>';
									echo '</li>';
								}
								?>
							</ul>
						</div>
					</div>
				</div>
				<div id="tabs-3" class="tab-pane">
                    <div class="collectibletypes">
                        <div class="title">
                            <h3><?php echo __('Collectible Platforms'); ?></h3>
                            <div class="btn-group actions">
                            	<?php echo $this -> Html -> link('<i class="icon-pencil icon-large"></i>', array('action' => 'add_collectibletype', $manufacture['Manufacture']['id']), array('class' => 'btn', 'escape' => false)); ?>  
                            </div>                              
                        </div>
                        <div class="standard-list">
                            <ul>
                                <?php
								foreach ($collectibletypes as $key => $collectibletype) {
									echo '<li>';
									echo '<span class="name">';
									echo $collectibletype['Collectibletype']['name'];
									echo '</span>';
									echo '</li>';
								}
                                ?>
                            </ul>
                        </div>
                    </div>					    
				</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="manufacturerDeleteModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
			×
		</button>
		<h3 id="myModalLabel">Delete Manufacturer</h3>
	</div>
	<div class="modal-body">
		<p>Are you sure you want to delete this manufacturer?</p>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">
			Close
		</button>
		<button class="btn btn-primary delete" autocomplete="off">
			Delete
		</button>
	</div>
</div>

<script>
				$(function() {
		$('#myTab a:first').tab('show');
	});
	
	$('.show-delete-modal').on('click', function(event){
		event.preventDefault();
		$('#manufacturerDeleteModal').modal();
	});
	$('.btn-primary.delete').on('click', function(event){
		event.preventDefault();
		window.location.href =  '/admin/manufactures/delete/' + <?php echo $manufacture['Manufacture']['id']; ?>
			;
			});

</script>