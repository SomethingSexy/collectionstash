<?php
echo $this -> Html -> script('views/view.filters.selected', array('inline' => false));
?>
<?php if(!empty($saveSearchFilters)){ ?>
<div class="row spacer">
    <div class="col-md-12">
        <div id="fancy-filters-selected" class="selected-search-filters">
            <?php
            foreach ($saveSearchFilters as $key => $value) {
            echo '<span class="filter label label-info" data-type="' . $value['type'] . '" data-id="' . $value['id'] . '">';
            echo $value['label'];
            echo '<span data-role="remove"></span>';
            echo '</span>';
            }
            ?>
        </div>
    </div>
</div>
<?php } ?>