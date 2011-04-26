<?php echo $this->Html->script('collectible-add',array('inline'=>false)); ?>

<div class="component" id="collectible-add-component">
  <div class="inside">
    <div class="component-title">
      <h2><?php __('Add Collectible');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>Select which type of collectible you are adding.</div> 
    </div>
    <div class="component-view">
      <?php echo $this->Form->create('Collectible');?>
        <fieldset>
          <ul class="form-fields">
            <li>
              <div class="label-wrapper">
                <label for=""><?php __('What are you trying to add?') ?></label>
                <a class="ui-icon ui-icon-info" title="<?php echo __('If you are adding an exclusive version or variant of a collectible, please select Collectible Variant.', true) ?>" alt="info"></a>
              </div>
              
            <?php 
            $options=array('C'=>'Collectible','V'=>'Collectible Variant');
            echo $this->Form->select('addType',$options,null,array('empty'=>'Select')); ?>
           </li>
          </ul>
        </fieldset>
      
        
      <?php echo $this->Form->end('Submit', array('class'=>'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only'));?>
    </div>    
  </div>
</div>
<script>
  $(".ui-icon-info").tooltip({ position: 'center right', opacity: 0.7});
</script>