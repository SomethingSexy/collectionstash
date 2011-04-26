<?php echo $this->Html->script('collectible-list',array('inline'=>false)); ?>
<div class="collectibles view">
  <div class="info">
  <h2>Pending Submission</h2>
  <div></div>
  </div>
  <table id="pending-submissions">
    <tr> 
      <th><?php echo $paginator->sort('Name', 'Collectible.name'); ?></th> 
      <th><?php echo $paginator->sort('Approval State', 'Approval.state'); ?></th> 
      <th><?php echo $paginator->sort('Approval User id', 'Approval.user_id'); ?></th> 
      <th>Collectible or Variant</th> 
      <th>&nbsp;</th> 
    </tr> 
       <?php foreach($collectibles as $collectible): ?> 
    <tr> 
    <?php 
      $variant = false;
      if(empty($collectible['Collectible']))
      {
        $variant = true;
      }
    
    //Using approval hasMany to get cleaner output, but it forces me to index but more than one should never be able to be added
      if($variant)
       { ?>
      
         <td><?php echo $collectible['Cvariant'][0]['Collectible']['name']; ?> </td>   
         
       <?php }
       else
       {  ?>
         <td><?php echo $collectible['Collectible'][0]['name']; ?> </td>  
       <?php } ?>
        <td><?php echo $collectible['Approval']['state']; ?> </td> 
        <td><?php echo $collectible['Approval']['user_id']; ?> </td> 
        <?php if($variant){ ?> <td>Variant</td>  <?php } else {?> <td>Collectible</td> <?php } ?>
      <td>
      <?php 
        if($variant)
        {
          echo $html->link('Approve', array('action' => 'view', 'id' => $collectible['Cvariant'][0]['id'], 'variant' => 'true'));  
        }
        else
        {
          echo $html->link('Approve', array('action' => 'view', 'id' => $collectible['Collectible'][0]['id'], 'variant' => 'false'));   
        }
      ?>
      </td>
      </tr> 
    <?php endforeach; ?> 
  </table> 
  <div class="paging">
    <p>
    <?php
    echo $this->Paginator->counter(array(
    'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
    ));
    ?>  </p>
    <?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
   |  <?php echo $this->Paginator->numbers();?>
 |
    <?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
  </div>

</div>
