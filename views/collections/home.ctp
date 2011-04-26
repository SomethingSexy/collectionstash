<?php echo $this->Html->script('collectible-list',array('inline'=>false)); ?>
<div class="container2">
  <div class="info">
  	<h2><?php __('My Profile');?></h2>
  	<div></div>
  </div>
</div>

<div class="container2">
  <div class="info">
  	<h2><?php __('My Stash Details');?></h2>
  	<div>You have <?php echo $stashCount ?> <?php if($stashCount==1){echo 'stash';}else{echo 'stashes';} ?>.</div>
  	<div>
  	   <table>
  	     <?php foreach($stashDetails as $details)
  	     {?>
  	      <tr>
          <th><?php echo $details['Stash']['name']; ?></th>
          <td>Has <?php echo $details['Stash']['count']; ?> collectibles.</td>
          <td><?php echo $html->link('View', array('controller' => 'collections',$details['Stash']['id'])); ?></td>
          </tr>
         <?php } ?>
         
           
  	   </table>
  	</div>
  </div>
</div>

