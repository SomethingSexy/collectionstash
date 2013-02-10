<div id="my-stashes-component" class="well">
    <h2><?php echo __('Stash', true) ?></h2> 
    <?php echo $this -> element('flash'); ?>
    <?php echo '<p class="">' . __('Nice try but you can\'t edit someone else\'s stash!', true) . '</p>'; ?>
</div>