<?php echo $this->Html->css('gallery/gallery',null,array('inline'=>false)); ?>

<h2>Something Sexy's Collection</h2>

<div class="gallery">
 <?php 
foreach ($collection as $thiscollectible)
{
?>

<div id="myImgBoxStyle-1" class="item">  
	<table cellspacing="0" cellpadding="0" border="0">         
	<tbody><tr><td width="100%">  
		<!-- start liquid corner box -->  
		<div class="top-left"></div><div class="top-right"></div>                                                          
		<div class="insideleft"><div class="insideright"><div class="inside">  
		         
			<?php echo $fileUpload->image($thiscollectible['Collectible']['Upload']['name'], array('width' => 200)); ?>
			        
		</div></div></div>        
		<div class="bottom-left"><div class="text-test">	<?php echo $thiscollectible['Collectible']['name']; ?></div></div><div class="bottom-right"></div>  
		<!-- end liquid corner box -->  
	</td></tr>  
	</tbody></table>  
</div>  

<?php }  	?>
</div>