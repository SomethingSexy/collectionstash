
<div class="two-column-page">
    <div class="inside">
        <?php echo $this -> element('admin_actions');?>
        <div class="page">
            <div class="title">
                <h2><?php echo __('Add Collectible Platform');?></h2>
            </div>
            <?php echo $this -> element('flash');?>
            <div class="series view">
                <?php echo $this -> Form -> create('Collectibletype');?>
                <fieldset>
                    <ul class="form-fields unstyled">
                        <li>
                            <?php echo $this -> Form -> input('name', array('label'=>__('Name'),'before' => '<div class="label-wrapper">','between'=>'</div>'));?>
                        </li>
                    </ul>
                </fieldset>
                <?php echo $this -> Form -> end(__('Submit', true));?>
            </div>
        </div>
    </div>
</div>
