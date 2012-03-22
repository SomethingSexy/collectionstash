<div id="admin-edit" class="two-column-page">
    <div class="inside">
        <?php echo $this -> element('admin_actions');?>
        <div class="page attributes-approval">
            <div class="title">
                <h2><?php echo __('Approval');?></h2>
            </div>
            <?php echo $this -> element('flash');?>
        
            <?php
                echo '<div class="standard-list tag-list">';
                echo '<ul>';
                echo '<li class="title">';
                echo '<span class="name">' . __('Tag', true) . '</span>';
                echo '<span class="action">' . __('Action', true) . '</span>';
                echo '</li>';
                echo '<li>' . '<span class="name">' . $tag['Tag']['tag'] . '</span>'; 
                echo '<span class="action">';
                if($tag['CollectiblesTag']['action'] === 'E'){
                    echo __('Edit', true);
                } else if($tag['CollectiblesTag']['action'] === 'D'){
                    echo __('Delete', true);
                }else if($tag['CollectiblesTag']['action'] === 'A'){
                    echo __('Add', true);
                }   
                echo '</span>';
                echo '</li>';           
                echo '</ul>';
                echo '</div>';
            ?>
         
            <div class="notes">
            <?php echo $this -> Form -> create('Approval', array('url'=>'/admin/edits/approval/'.$editId, 'id'=>'approval-form'));?>
                <input id="approve-input" type="hidden" name="data[Approval][approve]" value="" />
                <fieldset>
                    <ul class="form-fields">
                        <li>
                            <div class="label-wrapper">
                                <label for=""> <?php echo __('Notes')
                                    ?></label>
                            </div>
                            <textarea rows="6" cols="30" name="data[Approval][notes]"></textarea>
                        </li>   
                    </ul>
                </fieldset>         
            </form>
            </div>
            <div class="links">
                <input type="button" id="approval-button" class="button" value="Approve">
                <input type="button" id="deny-button" class="button" value="Deny">
            </div>
        <script>
            //Eh move this out of here
            $('#approval-button').click(function(){
                $('#approve-input').val('true');
                $('#approval-form').submit();   
            }); 
                $('#deny-button').click(function() {
                    $('#approve-input').val('false');
                    $('#approval-form').submit();
                });
        </script>
        </div>
    </div>
</div>