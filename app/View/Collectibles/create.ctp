<?php echo $this -> Html -> script('pages/page.collectible.create', array('inline' => false));?>

<?php
$output = $this -> Tree -> generate($collectibleTypes, array('id' => 'tree', 'model' => 'Collectibletype', 'element' => 'tree_create_collectibletype_node'));
$output = str_replace(array("\r\n", "\r", "\n"),'' , $output);
$lines = explode("\n", $output);
$new_lines = array();

foreach ($lines as $i => $line) {
    if(!empty($line))
        $new_lines[] = trim($line);
}


?>


<script>
	var collectiblTypeHtml = '<?php echo implode($new_lines); ?>';
</script>
   
<div id="create-container" class="row spacer">

</div>