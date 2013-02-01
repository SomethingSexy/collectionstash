<div class="component" id="collectibletypes-list-component">
  <div class="inside" >
     <div class="component-title">
      <h2><?php echo __('Submit New Collectible - Select Collectible Type');?></h2>
    </div>
    <?php echo $this->element('flash'); ?>
    <div class="component-info">
      <div>

      </div> 
    </div>    
    <div class="component-view">
        <?php echo $this -> Tree -> generate($collectibleTypes, array('id' => 'tree', 'model' => 'Collectibletype', 'element' => 'tree_create_collectibletype_node')); ?>
    </div>
  </div>
</div>