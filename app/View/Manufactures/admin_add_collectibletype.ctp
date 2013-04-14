<div class="two-column-page">
    <div class="inside">
        <?php echo $this -> element('admin_actions');?>
        <div class="page">
            <div class="title">
                <h2><?php echo __('Add Collectible Platform to Manufacturer');?></h2>
            </div>
            <?php echo $this -> element('flash');?>
            <div class="licenses view">
                <?php echo $this -> Form -> create('CollectibletypesManufacture', array('url' => '/admin/manufactures/add_collectibletype/' . $manufacturer_id, 'id' => 'add-form')); ?>
                <div class="standard-list">
                    <ul>
                    <?php
                        foreach ($collectibletypes as $key => $collectibletype) {
                            echo '<li>';
                            echo '<span class="name">';
                            echo $collectibletype['Collectibletype']['name'];
                            //cause I am lazy to make a better ui
                            echo  ' (';
                            echo 'id='.$collectibletype['Collectibletype']['id'].',';
                            if(empty($collectibletype['Collectibletype']['parent_id'])){
                                echo ' Parent';
                            } else {
                                echo 'parent_id='.$collectibletype['Collectibletype']['parent_id'];
                            }
                            echo  ')';
                            echo '</span>';
                            echo '<span class="action">';
                            echo '<input type="checkbox" name="data[CollectibletypesManufacture]['.$key.'][collectibletype_id]" value="'.$collectibletype['Collectibletype']['id'] .'"/>';
                            echo '</span>';                         
                            echo '</li>';   
                        }
                    ?>
                    </ul>
                </div>  

                <?php echo $this -> Form -> end(__('Add', true));?>
                
            </div>
        </div>
    </div>
</div>