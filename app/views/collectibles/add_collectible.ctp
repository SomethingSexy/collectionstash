<?php echo $this->Html->script('collectible-add',array('inline'=>false)); ?>
<div class="component" id="collectible-add-component">
  <div class="inside">
    <div class="component-title">
      <h2><?php __('Add Collectible');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>Fill out the following information to add the collectible for <?php echo $manufactureName; ?>.</div> 
    </div>
    <div class="component-view">
      <?php echo $this->Form->create('Collectible' , array('type' => 'file'));?>
        <fieldset>
          <ul class="form-fields">
            <li>
              <div class="label-wrapper">
                <label for="collectibleType"><?php __('What type of collectible is this?') ?></label>
              </div>
              <?php  echo $this->Form->select('Collectible.collectibletype_id',$collectibletypes,null,array('id'=>'collectibleType','label' => false,'empty' => false));  ?>
            </li>
            <li>
              <div class="label-wrapper">
                <label for="CollectibleName"><?php __('Name') ?></label>
              </div>              
              <?php echo $this->Form->input('name', array('div' =>  false, 'label' => false));?>  
            </li> 
            <li>
              <div class="label-wrapper">
                <label for="CollectibleCode"><?php __('Product Code') ?></label>
                <a class="ui-icon ui-icon-info" title="<?php echo __('This is the item code or product code given by the manufacture.', true) ?>" alt="info"></a>
              </div>
              <?php echo $this->Form->input('code', array('div' => false, 'label' => false,'between'=>''));?>
            </li>
            <li>
              <div class="label-wrapper">
                <label for="CollectibleUpc"><?php __('Product UPC') ?></label>
              </div>      
              <?php echo $this->Form->input('upc', array('div' => false, 'label' => false));?>
            </li>
            <li>
              <div class="label-wrapper">
                <label for="CollectibleDescription"><?php __('Description') ?></label>
              </div>      
              <?php echo $this->Form->input('description', array('div' => false, 'label' => false));?>
            </li> 
            <li>
              <div class="label-wrapper">
                <label for="CollectibleMsrp"><?php __('Original Retail Price') ?></label>
              </div> 
              <?php echo $this->Form->input('msrp', array('div' => false, 'label' => false));?>
            </li>
            <li>
              <div class="label-wrapper">
                <label for="CollectibleEditionSize"><?php __('Edition Size') ?></label>
                <a class="ui-icon ui-icon-info" title="<?php echo __('This is the edition size of the collectible.  If unknown, leave blank. If it has not been determined yet, enter \'TBD\'.  If there is no edition size, enter \'None\'', true) ?>" alt="info"></a>
              </div>            
              <?php echo $this->Form->input('edition_size', array('div' => false, 'label'=>false));?>
            </li>
            <li>
              <div class="label-wrapper">
                <label for="CollectibleProductWeight"><?php __('Weight (lbs)') ?></label>
              </div> 
              <?php echo $this->Form->input('product_weight', array('div' => false, 'label' => false));?>
            </li>
            <li>
              <div class="label-wrapper">
                <label for="collectibleHeight"><?php __('Height (inches)') ?></label>
              </div> 
              <?php echo $this->Form->input('product_length', array('div' => false,'label' => false, 'id'=>'collectibleHeight'));?>
            </li>
            <li id="widthWrapper">
              <div class="label-wrapper">
                <label for="collectibleWidth"><?php __('Width (inches)') ?></label>
              </div> 
              <?php echo $this->Form->input('product_width', array('div' => false, 'label' => false, 'id'=>'collectibleWidth'));?>
            </li>
            <li id="depthWrapper">
              <div class="label-wrapper">
                <label for="collectibleDepth"><?php __('Depth (inches)') ?></label>
              </div> 
              <?php echo $this->Form->input('product_depth', array('div' => false, 'label' => false, 'id'=>'collectibleDepth'));?>
            </li>
            <li>
              <div class="label-wrapper">
                <label for="CollectibleUrl"><?php __('Url') ?></label>
              </div> 
              <?php echo $this->Form->input('url', array('div' => false, 'label' => false));?>
            </li> 
            <li>
              <div class="label-wrapper">
                <label for="Upload0File"><?php __('Image') ?></label>
              </div> 
              <?php echo $this->Form->input('Upload.0.file', array('div' => false, 'type' => 'file', 'label' => false));?>
            </li>
          </ul>
          
          <script>
$(".ui-icon-info").tooltip({ position: 'center right', opacity: 0.7});
</script>

        </fieldset>
      <?php echo $this->Form->end(__('Submit', true));?>
          </div>    
  </div>
</div>