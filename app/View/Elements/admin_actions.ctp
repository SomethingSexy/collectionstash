<div class="page actions">
	<ul class="unstyled">
		<li>
			<h3><?php echo __('Collectibles');?></h3>
			<ul class="unstyled">
				<li>
					<?php echo $this -> Html -> link('View Pending', array('admin' => true, 'controller' => 'collectibles'), array('class' => 'link'));?>
				</li>
			</ul>
		</li>
		<li>
			<h3><?php echo __('Items');?></h3>
			<ul class="unstyled">
				<li>
					<?php echo $this -> Html -> link('View Standalone Pending', array('admin' => true, 'controller' => 'attributes', true), array('class' => 'link'));?>
				</li>
				<li>
					<?php echo $this -> Html -> link('View Collectible Pending', array('admin' => true, 'controller' => 'attributes', false), array('class' => 'link'));?>
				</li>
			</ul>
		</li>
		<li>
			<h3><?php echo __('Edits');?></h3>
			<ul class="unstyled">
				<li>
					<?php echo $this -> Html -> link('View', array('admin' => true, 'controller' => 'edits', 'action'=> 'index'), array('class' => 'link'));?>
				</li>
			</ul>			
		</li>
		<li>
			<h3><?php echo __('Manufacturers');?></h3>
			<ul class="unstyled">
				<li>
					<?php echo $this -> Html -> link('Add', array('admin' => true, 'controller' => 'manufactures', 'action' => 'add'), array('class' => 'link'));?>
				</li>
				<li>
					<?php echo $this -> Html -> link('Edit', array('admin' => true, 'controller' => 'manufactures', 'action' => 'list'), array('class' => 'link'));?>
				</li>
			</ul>
		</li>
        <li>
            <h3><?php echo __('Brands');?></h3>
            <ul class="unstyled">
                <li>
                    <?php echo $this -> Html -> link('Add', array('admin' => true, 'controller' => 'licenses', 'action' => 'add'), array('class' => 'link'));?>
                </li>
                <li>
                    <?php echo $this -> Html -> link('Edit', array('admin' => true, 'controller' => 'licenses', 'action' => 'list'), array('class' => 'link'));?>
                </li>
            </ul>
        </li>
        <li>
            <h3><?php echo __('Collectible Types');?></h3>
            <ul class="unstyled">
                <li>
                    <?php echo $this -> Html -> link('Add', array('admin' => true, 'controller' => 'collectibletypes', 'action' => 'add'), array('class' => 'link'));?>
                </li>
                <li>
                    <?php echo $this -> Html -> link('Edit', array('admin' => true, 'controller' => 'collectibletypes', 'action' => 'list'), array('class' => 'link'));?>
                </li>
            </ul>
        </li>
		<li>
			<h3><?php echo __('Categories');?></h3>
			<ul class="unstyled">
				<li>
					<?php echo $this -> Html -> link('Add', array('admin' => true, 'controller' => 'series', 'action' => 'add'), array('class' => 'link'));?>
				</li>
				<li>
					<?php echo $this -> Html -> link('Edit', array('admin' => true, 'controller' => 'series', 'action' => 'list'), array('class' => 'link'));?>
				</li>
			</ul>
		</li>
	</ul>
</div>