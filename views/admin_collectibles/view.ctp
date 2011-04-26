<?php //TODO need to add photo ?>
<div class="container2">
  <div class="info">
    <h2><?php  __('Submitted Collectible');?></h2>
  </div>
<div class="collectibles view">
  <table>
    <caption>Details<caption>
    <tr>
      <th><?php __('Name'); ?></th>
      <td><?php echo $collectible['Collectible']['name']; ?></td>
     </tr>
     <tr>
      <th><?php __('Manufacture'); ?></th>
      <td>
        <?php echo $this->Html->link($collectible['Manufacture']['title'], array('controller' => 'manufactures', 'action' => 'view', $collectible['Manufacture']['id'])); ?>
      </td>
    </tr>
    <tr>
      <th><?php __('License'); ?></th>
      <td>
        <?php echo $collectible['License']['name']; ?>
      </td>
    </tr>
    <tr>
      <th><?php __('Description'); ?></th>
      <td>
        <?php echo $collectible['Collectible']['description']; ?>
      </td>
    </tr>
    <?php if(!empty($collectible['Collectible']['code'])){ ?>
    <tr>
        <th><?php __('Product Id'); ?></th>
        <td>
          <?php echo $collectible['Collectible']['code']; ?>
        </td> 
      </tr>  
    <?php } ?>
    <tr>
      <th><?php __('Type'); ?></th>
      <td>
        <?php echo $collectible['Collectibletype']['name']; ?>
      </td>
    </tr>
    <tr>
      <th><?php __('Original Retail Price'); ?></th>
      <td>
        <?php echo $collectible['Collectible']['msrp']; ?>
      </td>
    </tr>
    <tr>
      <th><?php __('Edition Size'); ?></th>
      <td>
        <?php echo $collectible['Collectible']['edition_size']; ?>
      </td>
    </tr>
    <tr>
      <th><?php __('Dimensions'); ?></th>
      <td>
        <?php echo $collectible['Collectible']['product_length']; ?> x <?php echo $collectible['Collectible']['product_width']; ?> x <?php echo $collectible['Collectible']['product_depth']; ?> 
      </td>
    </tr>
  </table>

</div>
</div>


<div class="container2">
  <div class="info">
  <h2>Approval Details</h2>
  <div></div>
  </div>
<?php echo $this->Form->create('Approval',array('url' => array('controller'=>'adminCollectibles','action'=>'approve','id' => $collectible['Approval']['id'],'collectibleid'=>$collectible['Collectible']['id'])));?>
  <fieldset>
    <ul class="form-fields">
      <li><?php echo $this->Form->input('notes', array('div' => false, 'label' => 'Notes'));?></li>
    </ul>
  </fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>