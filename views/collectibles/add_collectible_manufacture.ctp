<?php echo $this->Html->script('collectible-add',array('inline'=>false)); ?>
<div class="component" id="collectible-add-component">
  <div class="inside">
    <div class="component-title">
      <h2><?php __('Add Collectible - Select Manufacture');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>Select the manufacture of the collectible you wish to add.</div> 
    </div>
    <div class="component-view">
      <?php echo $this->Form->create('Collectible');?>
        <fieldset>
          <ul class="form-fields">
            <li>
              <div class="label-wrapper">
                <label for=""><?php __('Who makes this collectible?') ?></label>
              </div>
              <?php echo $this->Form->input('manufacture_id', array('div' => false, 'label' => false));?>
            </li>
          </ul>
        </fieldset>
      <?php echo $this->Form->end(__('Submit', true));?>
    </div>    
  </div>
</div>
<script>
  $(".ui-icon-info").tooltip({ position: 'center right', opacity: 0.7});
</script>