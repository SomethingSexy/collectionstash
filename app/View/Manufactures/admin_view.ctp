<div class="two-column-page">
	<div class="inside">
		<?php echo $this -> element('admin_actions');?>
		<div class="page">
			<div class="title">
				<h2><?php echo __('Manufacturer Detail');?></h2>
				<div class="actions icon">
					<ul>
						<li>
							<a href="/admin/manufactures/edit/<?php echo $manufacture['Manufacture']['id'];?>"> <i class="icon-pencil icon-large"></i> </a>
						</li>
					</ul>
				</div>
			</div>
			<?php echo $this -> element('flash');?>
			<div class="manufacturer view">
				<div class="manufacturer detail">
					<dl>
						<dt>
							<?php echo __('Title');?>
						</dt>
						<dd>
							<?php echo $manufacture['Manufacture']['title'];?>
						</dd>
						<dt>
							<?php echo __('URL');?>
						</dt>
						<dd>
							<?php echo $manufacture['Manufacture']['url'];?>
						</dd>
						<dt>
							<?php echo __('Collectible Count');?>
						</dt>
						<dd>
							<?php echo $manufacture['Manufacture']['collectible_count'];?>
						</dd>
						<dt>
							<?php echo __('Series Id');?>
						</dt>
						<dd>
							<?php echo $manufacture['Manufacture']['series_id'];?>
						</dd>
					</dl>
				</div>
				<div id="tabs">
					<ul>
						<li>
							<a href="#tabs-2"><?php echo __('Brands');?></a>
						</li>
						<li>
							<a href="#tabs-3"><?php echo __('Collectible Types');?></a>
						</li>
					</ul>
					<div id="tabs-1"></div>
					<div id="tabs-2">
						<div class="licenses">
						    <div class="title">
                                <h3><?php echo __('Brands');?></h3>
                                <div class="actions icon">
                                    <ul>
                                        <li><?php echo $this -> Html -> link($this->Html->image('/img/icon/add.png'),array('action' => 'add_license', $manufacture['Manufacture']['id']), array('escape' => false));?></li>  
                                    </ul>
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
					<div id="tabs-3">
                        <div class="collectibletypes">
                            <div class="title">
                                <h3><?php echo __('Collectible Types');?></h3>
                                <div class="actions icon">
                                    <ul>
                                        <li><?php echo $this -> Html -> link($this->Html->image('/img/icon/add.png'),array('action' => 'add_collectibletype', $manufacture['Manufacture']['id']), array('escape' => false));?></li>  
                                    </ul>
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
<script>
	$(function() {
		$("#tabs").tabs();
	});

</script>