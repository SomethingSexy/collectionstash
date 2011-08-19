<div class="component">
	<div class="inside" >
	  <div class="component-title">
	    <h2><?php __('Add to Stash') ?></h2> 
	  </div>		
		<?php echo $this -> element('flash');?>		
	  <div class="component-info">
	    <div><?php __('Tell us about your collectible.') ?></div> 
	  </div>
	  <div class="component-view">
	    <?php echo $this->Form->create('CollectiblesUser', array('url'=>'/collectiblesUser/add'));?>
	      <fieldset>
	        <ul class="form-fields">
	          <li>
	            <div class="label-wrapper">
	              <label for="dialogEditionSize"><?php __('Edition Number') ?></label>
	            </div> 
	            <?php echo $this->Form->input('edition_size', array('id'=>'dialogEditionSize','div' => false, 'label' => false)); ?>
	          </li>
	          <li>
	            <div class="label-wrapper">
	              <label for="dialogCost"><?php __('How much did you pay? (USD)') ?></label>
	            </div> 
	            <?php echo $this->Form->input('cost', array('id'=>'dialogCost','div' =>  false, 'label' => false));?>
	          </li> 
	          <li>
	            <div class="label-wrapper">
	              <label for="CollectiblesUserConditionId"><?php __('Condition') ?></label>
	            </div> 
	            <?php echo $this->Form->input('condition_id', array('div' =>  false, 'label' => false));?>
	          </li> 
	          <li>
	            <div class="label-wrapper">
	              <label for="CollectiblesUserMerchantId"><?php __('Where did you purchase the collectible?') ?></label>
	            </div> 
	            <?php echo $this->Form->input('merchant_id', array('div' =>  false, 'label' => false));?>
	          </li> 
	          <?php echo $this->Form->hidden('CollectiblesUser.collectible_id');?>
	        </ul>
	      </fieldset>
	    <?php echo $this->Form->end('Add');?>          
	  </div>
	</div>
</div>
