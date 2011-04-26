<?php echo $this->Html->script('variant-add',array('inline'=>false)); ?>
<div class="component">
	<div class="component-title">
      <h2><?php echo $collectible['Collectible']['name']; ?><?php if($collectible['Collectible']['exclusive']){ __(' - Exclusive'); } ?> </h2>
    </div>	
  	<div class="collectible item">
	    <div class="collectible image"><?php echo $fileUpload->image($collectible['Upload'][0]['name'], array('width' => '100')); ?>
	      <div class="collectible image-fullsize hidden"><?php echo $fileUpload->image($collectible['Upload'][0]['name'], array('width' => 0)); ?></div>
	      </div>
	      <div class="collectible detail">
	       <dl>
	         <dt>Name: </dt><dd><?php echo $collectible['Collectible']['name']; ?><?php if($collectible['Collectible']['exclusive']){ __(' - Exclusive'); } ?> </dd>
	         <?php
	           if ($collectible['Collectible']['variant'])
	           {
	             echo '<dt>';
	             __('Variant:');
	             echo '</dt><dd>';
	             __('Yes');
	             echo '</dd>';
	             
	             
	           }
	         ?>        
	         <dt>Manufacture: </dt><dd><a target="_blank" href="<?php echo $collectible['Manufacture']['url']; ?>"><?php echo $collectible['Manufacture']['title']; ?></a></dd>
	         <dt>Type: </dt><dd><?php echo $collectible['Collectibletype']['name']; ?></dd>
	       </dl>
	    </div>
  	</div>
</div>

<div class="component" id="collectible-add-component">
  <div class="inside">
    <div class="component-title">
      <h2><?php __('Add Collectible Variant');?></h2>
    </div>
    <div class="component-info">
      <div><?php __('Fill out the information below to add a variant for the following collectible.');?></div> 
    </div>
    <div class="component-view">
      <?php echo $this->Form->create('Collectible', array('type' => 'file', 'url' => '/collectibles/addVariant'));?>
        <fieldset>
        <ul class="form-fields">
          <li>
            <div class="label-wrapper">
               <label for="CollectibleName"><?php __('Name') ?></label>
            </div>
            <?php echo $this->Form->input('name', array('value'=> $collectible['Collectible']['name'], 'div' =>  false, 'label'=>false));?>
          </li> 
          <li>
            <div class="label-wrapper">
               <label for="CollectibleExclusive"><?php __('Exclusive') ?></label>
            </div>
            <?php echo $this->Form->input('exclusive', array('div' => false, 'label' => false )); ?>
          </li>
          <li>
            <div class="label-wrapper">
               <label for="CollectibleEditionSize"><?php __('Edition size') ?></label>
            </div>
            <?php echo $this->Form->input('edition_size', array('div' => false, 'label' => false )); ?>
          </li>
          <li>
            <div class="label-wrapper">
               <label for="CollectibleUrl"><?php __('URL') ?></label>
            </div>
            <?php echo $this->Form->input('url', array('div' => false, 'label' => false )); ?>
          </li>
          <li>
            <div class="label-wrapper">
               <label for="Upload0File"><?php __('Image') ?></label>
            </div>
            <?php echo $this->Form->input('Upload.0.file', array('div' => false, 'type' => 'file', 'label'=> false));?>
          </li>
          <li>
          	 <div class="label-wrapper">
               <label for=""><?php __('Attributes') ?></label>
               <a class="ui-icon ui-icon-info" title="<?php echo __('Select add, to add an attribute for this collectible.  An attribute is a way to define what makes this collectible an exclusive or variant.', true) ?>" alt="info"></a>
            </div>
            <div id="add-attributes-list">
            	<ul>
            		<?php  
            			
            			if(isset($this->data['AttributesCollectible']))
						{
							$lastKey = 1;
							foreach ($this->data['AttributesCollectible'] as $key=>$attribue)
						    {
						      echo '<li>';	
							  echo '<span class="attribute-name">';
							  echo $attribue['name'];
							  echo '</span>';
							  echo '<span class="attribute-description">';
							  echo $attribue['description'];
							  echo '</span>';
							  echo '<input type="hidden" name="data[AttributesCollectible]['.$key.'][attribute_id]" value="'.$attribue['attribute_id'].'"/>';
							  echo '<input type="hidden" name="data[AttributesCollectible]['.$key.'][description]" value="'.$attribue['description'].'"/>';
							  echo '<input type="hidden" name="data[AttributesCollectible]['.$key.'][name]" value="'.$attribue['name'].'"/>';
							  echo '</li>';
							  $lastKey = $key;
						    }
							echo '<script>var lastAttributeKey ='.$lastKey.';</script>';
						}
						
            		?>
            	</ul>
            </div>
            <div><a class="ui-icon ui-icon-plus add-attribute">Add Attribute</a></div>
          </li>
        </ul>
        </fieldset>
      <?php echo $this->Form->end(__('Submit', true));?>    
    </div>
  </div>
</div>

<div id="add-attribute-dialog" class="dialog" title="Add Attribute">
  <div class="component">
    <div class="inside" >
      <div class="component-info">
        <div><?php __('Fill out the information below to add an Attribute to this variant.') ?></div> 
      </div>
      <div class="component-view">
        <fieldset>
          <ul id="add-attribute-dialog-fields" class="form-fields">
            <li id="description-field">
              <div class="label-wrapper">
                 <label for="CollectibleName"><?php __('Description') ?></label>
              </div>
              <?php echo $this->Form->input('description', array('maxlength'=> 50, 'id' => 'attributeDescription','div' =>  false, 'label'=>false));?>
            </li>             
          </ul>
        </fieldset>
      </div>
    </div>
  </div>
</div>


