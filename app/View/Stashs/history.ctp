<?php echo $this -> Minify -> script('js/thirdparty/jquery.flot', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/thirdparty/jquery.flot.categories', array('inline' => false)); ?>
<?php echo $this -> Minify -> script('js/thirdparty/jquery.flot.stack', array('inline' => false)); ?>
<?php echo $this -> Html -> script('views/view.stash.remove', array('inline' => false)); ?>
<?php echo $this -> Html -> script('models/model.collectible.user', array('inline' => false)); ?>
<?php echo $this -> Html -> script('cs.stash', array('inline' => false)); ?>
<h2><?php
echo $stashUsername . '\'s History';
?></h2>

<div id="my-stashes-component" class="widget widget-tabs">
		<?php echo $this -> element('flash'); ?>
		<ul class="nav nav-tabs widget-wide">
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
			<li class="active"><?php echo '<a href="/stashs/history/' . $stashUsername . '">' . __('History') . '</a>'; ?></li>
		</ul>			
	<div class="row">
		<div class="col-md-12">
			<div class="">
				<h2>Bought and Sold</h2>
				<div class="btn-group years">
				<?php
				$default = key($graphData);
				foreach ($graphData as $keyYear => $year) {
					echo '<a data-key="' . $keyYear . '" class="btn';
					if ($keyYear === $default) {
						echo ' active';
					}
					echo '">' . $keyYear . '</a>';
				}
				?> 
			    </div>	
			    <div class="graph-container">
					<div id="holder" style="width:850px;height:450px">
						<?php
						if (empty($graphData)) {
							echo '<p>' . __(' Not enough information to draw Bought and Sold graph.', true) . '</p>';
						}
					?>
					</div>			    	
			    </div>

			</div>
		</div>
	</div>

					<?php
					if (isset($collectibles) && !empty($collectibles)) {
						echo $this -> element('stash_table_list', array('collectibles' => $collectibles, 'showThumbnail' => false, 'stashType' => 'default', 'history' => true));
					} else {
						echo '<p>' . $stashUsername . __(' has no collectibles in their stash!', true) . '</p>';
					}
	?>
	
</div>

<script><?php
if (isset($reasons)) {
	echo 'var reasons = \'' . json_encode($reasons) . '\';';
}
	?><?php
	$counts = '';

	foreach ($graphData as $keyYear => $year) {
		$counts .= $keyYear . ':[[';
		foreach ($year as $keyMonth => $month) {
			$monthLabel = $keyMonth;
			// this is caca but will work for now
			switch ($keyMonth) {
				case 1 :
					$monthLabel = '"January"';
					break;
				case 2 :
					$monthLabel = '"February"';
					break;
				case 3 :
					$monthLabel = '"March"';
					break;
				case 4 :
					$monthLabel = '"April"';
					break;
				case 5 :
					$monthLabel = '"May"';
					break;
				case 6 :
					$monthLabel = '"June"';
					break;
				case 7 :
					$monthLabel = '"July"';
					break;
				case 8 :
					$monthLabel = '"August"';
					break;
				case 9 :
					$monthLabel = '"September"';
					break;
				case 10 :
					$monthLabel = '"October"';
					break;
				case 11 :
					$monthLabel = '"November"';
					break;
				case 12 :
					$monthLabel = '"December"';
					break;
			}

			$counts .= '[' . $monthLabel . ' ,' . count($month['purchased']) . '],';
		}
		$counts .= '],[';
		foreach ($year as $keyMonth => $month) {
			$monthLabel = $keyMonth;
			// this is caca but will work for now
			switch ($keyMonth) {
				case 1 :
					$monthLabel = '"January"';
					break;
				case 2 :
					$monthLabel = '"February"';
					break;
				case 3 :
					$monthLabel = '"March"';
					break;
				case 4 :
					$monthLabel = '"April"';
					break;
				case 5 :
					$monthLabel = '"May"';
					break;
				case 6 :
					$monthLabel = '"June"';
					break;
				case 7 :
					$monthLabel = '"July"';
					break;
				case 8 :
					$monthLabel = '"August"';
					break;
				case 9 :
					$monthLabel = '"September"';
					break;
				case 10 :
					$monthLabel = '"October"';
					break;
				case 11 :
					$monthLabel = '"November"';
					break;
				case 12 :
					$monthLabel = '"December"';
					break;
			}
			$counts .= '[' . $monthLabel . ' ,' . count($month['sold']) . '],';
		}
		$counts .= ']],';
	}
	echo 'var data = {' . $counts . '};';
	echo 'var bdata = JSON.parse(JSON.stringify(data["' . $default . '"]));';
?>
	$(function() {
		$.plot("#holder", bdata, {
			series : {
				stack : false,
				bars : {
					show : true,
					barWidth : 0.6,
					align : "center"
				}
			},
			xaxis : {
				mode : "categories",
				tickLength : 0
			},
			yaxis : {
				minTickSize : 1
			}
		});

		$('.years').on('click', '.btn', function(event) {
			$(event.currentTarget).siblings().removeClass('active');
			$(event.currentTarget).addClass('active');
			bdata = JSON.parse(JSON.stringify(data[$(event.currentTarget).attr('data-key')]));
			$.plot("#holder", bdata, {
				series : {
					stack : false,
					bars : {
						show : true,
						barWidth : 0.6,
						align : "center"
					}
				},
				xaxis : {
					mode : "categories",
					tickLength : 0
				},
				yaxis : {
					minTickSize : 1
				}
			});
		});
	});
</script>