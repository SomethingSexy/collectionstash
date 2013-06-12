<?php echo $this -> Minify -> script('js/thirdparty/raphael', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/thirdparty/g.raphael', array('inline' => false)); ?>
<?php echo $this -> Html -> script('thirdparty/g.bar', array('inline' => false)); ?>
<?php echo $this -> Html -> script('views/view.stash.remove', array('inline' => false)); ?>
<?php echo $this -> Html -> script('models/model.collectible.user', array('inline' => false)); ?>
<?php echo $this -> Html -> script('cs.stash', array('inline' => false)); ?>


<div id="my-stashes-component" class="span12">

		<div class="page-header">
			<h1><?php echo __('History'); ?></h1>
		</div>
		<?php echo $this -> element('flash'); ?>
		<div class="row-fluid">
			<div class="span8">
				<div class="actions stash">
					<ul class="nav nav-pills">
						<?php
						echo '<li>';
						?>
						
						<?php echo '<a href="/stash/' . $stashUsername . '">' . __('Collectibles') . '</a>'; ?>
						</li>
						<?php
						echo '<li>';
						?>
						<?php echo '<a href="/wishlist/' . $stashUsername . '">' . __('Wishlist') . '</a>'; ?>
						</li>
						<li>
						<?php echo '<a href="/user_uploads/view/' . $stashUsername . '">' . __('Photos') . '</a>'; ?>	
						</li>
						<li><?php echo '<a href="/stashs/comments/' . $stashUsername . '">' . __('Comments') . '</a>'; ?></li>
						<li class="selected"><?php echo '<a href="/stashs/history/' . $stashUsername . '">' . __('History') . '</a>'; ?></li>
					</ul>	
				</div>
				
			</div>
			<div class="span4">
				<!--
				<div class="btn-group pull-right years">
				    <button class="btn">All</button>
				    <button class="btn">Sold</button>
			    </div>			-->	
			</div>
			
		</div>
			
		

			
	<div class="row-fluid">
		<div class="span6">
			<div class="well">
				<h2>Bought and Sold</h2>
				<div class="btn-group pull-right years">
				<?php
				$default = key($graphData);
				foreach ($graphData as $keyYear => $year) {
					echo '<button data-key="' . $keyYear . '" class="btn';
					if ($keyYear === $default) {
						echo ' active';
					}
					echo '">' . $keyYear . '</button>';
				}
				?> 
			    </div>	
				<div id="holder">
					<?php if(empty($graphData)) {
						echo '<p>'  . __(' Not enough information to draw Bought and Sold graph.', true) . '</p>';
					}?>
				</div>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span6 well">
			<div class="">
					<?php
					if (isset($collectibles) && !empty($collectibles)) {
						echo $this -> element('stash_table_list', array('collectibles' => $collectibles, 'showThumbnail' => false, 'stashType' => 'default' , 'history' => true));
					} else {
							echo '<p>' . $stashUsername . __(' has no collectibles in their stash!', true) . '</p>';
					}
	?>
			</div>
		</div>
	</div>
</div>

<script>
<?php
	if (isset($reasons)) {
		echo 'var reasons = \'' . json_encode($reasons) . '\';';
	}
	?>	
<?php
$counts = '';

foreach ($graphData as $keyYear => $year) {
	$counts .= $keyYear . ':[[';
	foreach ($year as $keyMonth => $month) {
		$counts .= '' . count($month['purchased']) . ',';
	}
	$counts .= '],[';
	foreach ($year as $keyMonth => $month) {
		$counts .= '' . count($month['sold']) . ',';
	}
	$counts .= ']],';
}
echo 'var data = {' . $counts . '};';
echo 'var bdata = JSON.parse(JSON.stringify(data["' . $default . '"]))';
?>
	// var b1data = [[319309], [305303], [534917]];
	// var b2data = [[268210], [263097], [359183]];
	// var b3data = [[373217], [064199], [201510]];
	$(function() {
		var r = Raphael('holder', 980, 500);

		var months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
		var fin = function() {
			this.flag = r.popup(this.bar.x, this.bar.y, this.bar.value || "0", 'right').insertBefore(this);
		};
		var fout = function() {
			this.flag.animate({
				opacity : 0
			}, 300, function() {
				this.remove();
			});
		};
		var c = r.barchart(0, 0, 960, 400, bdata, {
			stacked : true,
			axis : "0 0 1 1",
			axisxlabels : ["2008", "2009", "2010"]
		}).label(months, true).hover(fin, fout);

		function b_animate() {
			var c2 = r.barchart(0, 0, 960, 400, bdata, {
				stacked : true,
			}).hover(fin, fout);

			c.remove();
			c = c2;
			//$.each(c.bars[0], function(k, v) {
			//	v.animate({
			//		path : c2.bars[0][k].attr("path")
			//	}, 500);
			//	v.value[0] = bdata[k][0];
			//});
			//c2.remove();
		}


		$('.years').on('click', '.btn', function(event) {
			$(event.currentTarget).siblings().removeClass('active');
			$(event.currentTarget).addClass('active');
			bdata = JSON.parse(JSON.stringify(data[$(event.currentTarget).attr('data-key')]));
			b_animate();
		});
	}); 
</script>