<table>
<?php 

echo $html->tableHeaders(array_keys($collection[0]['Collectible']));

foreach ($collection as $thiscollectible)
{
	echo $html->tableCells($thiscollectible['Collectible']);
}

?>
</table>
