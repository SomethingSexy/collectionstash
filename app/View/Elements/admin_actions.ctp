<div class="page actions">
	<ul>
		<li>
			<h3><?php echo __('Collectibles');?></h3>
			<ul>
				<li>
					<?php echo $this -> Html -> link('Pending', array('admin' => true, 'controller' => 'collectibles'), array('class' => 'link'));?>
				</li>
				<li>
					<?php echo $this -> Html -> link('Edits', array('admin' => true, 'controller' => 'edits', 'action'=> 'index'), array('class' => 'link'));?>
				</li>
			</ul>
		</li>
		<li>
			<h3><?php echo __('Manufacturers');?></h3>
			<ul>
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
            <ul>
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
            <ul>
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
			<ul>
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