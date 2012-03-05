<div class="two-column-page">
    <div class="inside">
        <?php echo $this -> element('admin_actions');?>
        <div class="page">
            <div class="title">
                <h2><?php echo __('Edit Brand');?></h2>
            </div>
            <?php echo $this -> element('flash');?>
            <div class="licenses view">
                <?php echo $this -> Form -> create('License');?>
                <fieldset>
                    <ul class="form-fields">
                        <li>
                            <?php echo $this -> Form -> input('name', array('label'=>__('Name'),'before' => '<div class="label-wrapper">','between'=>'</div>'));?>
                        </li>
                        <?php echo $this -> Form -> hidden('id')?>
                    </ul>
                </fieldset>
                <?php echo $this -> Form -> end(__('Submit', true));?>
            </div>
        </div>
    </div>
</div>