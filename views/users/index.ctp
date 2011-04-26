<table>
<?php 

echo $html->tableHeaders(array_keys($knownusers[0]['User']));

foreach ($knownusers as $thisuser)
{
	echo $html->tableCells($thisuser['User']);
}

?>
</table>
