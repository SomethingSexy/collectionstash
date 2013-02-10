<div id="admin-edit" class="two-column-page">
    <div class="inside">
        <?php echo $this -> element('admin_actions'); ?>
        <div class="page attributes-approval">
            <div class="title">
                <h2><?php echo __('Approval'); ?></h2>
            </div>
            <?php echo $this -> element('flash'); ?>
        
            <?php
			echo '<div class="standard-list tag-list">';
			echo '<table class="table"><thead>';
			echo '<tr class="title">';
			echo '<th class="name">' . __('Artist', true) . '</th>';
			echo '<th class="action">' . __('Action', true) . '</th>';
			echo '</tr></thead><tbody>';
			echo '<tr>' . '<td class="name">' . $artist['Artist']['name'] . '</td>';
			echo '<td class="action">';
			if ($artist['Action']['action_type_id'] === '2') {
				echo __('Edit', true);
			} else if ($artist['Action']['action_type_id'] === '4') {
				echo __('Delete', true);
			} else if ($artist['Action']['action_type_id'] === '1') {
				echo __('Add', true);
			}
			echo '</td>';
			echo '</tr>';
			echo '</tbody></table>';
			echo '</div>';
            ?>
         
            <div class="notes">
            <?php echo $this -> Form -> create('Approval', array('url' => '/admin/edits/approval_2/' . $editId, 'id' => 'approval-form')); ?>
                <input id="approve-input" type="hidden" name="data[Approval][approve]" value="" />
                <fieldset>
                    <ul class="form-fields unstyled">
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
				<button id="approval-button" class="btn btn-primary"><?php echo __('Approve'); ?></button>
				<button id="deny-button" class="btn"><?php echo __('Deny'); ?></button>
            </div>
        <script>
			//Eh move this out of here
			$('#approval-button').click(function() {
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