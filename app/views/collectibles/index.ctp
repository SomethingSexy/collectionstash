<?php echo $this->Html->script('collectible-list',array('inline'=>false)); ?>
<div class="collectibles view">
	<h2><?php __('Collectibles');?></h2>
	<?php echo $this->Form->create(false , array('url' => '/collectibles'));?>
	 <fieldset>
      <ul class="form-fields">
        <li><?php echo $this->Form->input('search');?></li>
	    </ul>
	  </fieldset>
	 <?php echo $this->Form->end(__('Submit', true));?>
	<?php
	foreach ($collectibles as $collectible):
	?>
	<div class="collectible item">
	  <div class="collectible image"><?php echo $fileUpload->image($collectible['Upload'][0]['name'], array('width' => 100)); ?>
	   <div class="collectible image-fullsize hidden"><?php echo $fileUpload->image($collectible['Upload'][0]['name'], array('width' => 0)); ?></div>
	  </div>
	  <div class="collectible detail">
       <dl>
	     <dt>Name: </dt><dd><?php echo $collectible['Collectible']['name']; ?></dd>
	     <dt>Manufacture: </dt><dd><a target="_blank" href="<?php echo $collectible['Manufacture']['url']; ?>"><?php echo $collectible['Manufacture']['title']; ?></a></dd>
	     <dt>Type: </dt><dd><?php echo $collectible['Collectibletype']['name']; ?></dd>
       <dt>Description: </dt><dd><?php echo $collectible['Collectible']['description']; ?></dd>
       <dt><a href="<?php echo $collectible['Collectible']['url']; ?>" target="_blank">Product Link</a></dd>
     </dl>
	 </div>
	 	<div class="links">
	    <ul>
	     <li><?php echo $html->link('Details', array('controller' => 'collectibles', 'action' => 'view', $collectible['Collectible']['id'])); ?></li>   
	    </ul>
	 </div>
  </div>
  <?php if(!empty($collectible['Cvariant']))
        {
         	foreach ($collectible['Cvariant'] as $variant): ?> 
        	<div class="collectible item">
        	  <div class="collectible image"><?php echo $fileUpload->image($variant['Upload']['name'], array('width' => 100)); ?>
              <div class="collectible image-fullsize hidden"><?php echo $fileUpload->image($variant['Upload']['name'], array('width' => 0)); ?></div>
            </div>
        	  <div class="collectible detail">

              <dl>
        	     <dt>Name: </dt><dd><?php echo $collectible['Collectible']['name']; ?>
        	     <?php 
                  if($variant['exclusive'])
                  {
                    echo " - Exclusive";
                  }
               
               ?>               
               </dd>
        	     <dt>Manufacture: </dt><dd><a target="_blank" href="<?php echo $collectible['Manufacture']['url']; ?>"><?php echo $collectible['Manufacture']['title']; ?></a></dd>
        	     <dt>Type: </dt><dd><?php echo $collectible['Collectibletype']['name']; ?></dd>
               <dt>Description: </dt><dd><?php echo $collectible['Collectible']['description']; ?></dd>
               <dt><a href="<?php echo $variant['url']; ?>" target="_blank">Product Link</a></dd>
             </dl> 
               
          
        	 </div>
          </div>
       <?php endforeach; ?> 
       <?php }  ?>
<?php endforeach; ?>
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
