<?php 

echo $this -> Html -> script('jquery.treeview', array('inline' => false));

?>
<?php 
echo $this -> Tree ->generate($stuff, array('id' => 'tree')); 



?> 

<script>
$(function(){
	$("#tree").treeview();
});

</script>