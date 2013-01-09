<div class="well spacer">
	<h2>Name the Collection Stash Squirrel</h2>
	<?php echo $this -> element('flash'); ?>
	<p>
		You can participate in Collection Stash history by helping us name the Squirrel.
	</p>
	<p>
		The following names have been submitted by the community for you to vote on.  You can only vote once so make it count (also make sure you are logged in!).
	</p>
	<p>
		Voting will end February 1st and the winner will be announced!
	</p>
	<table class="table table-striped">
		<thead>
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Vote</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($poll['PollOption'] as $key => $pollOption) {
				echo '<tr>';
				echo '<td>' . $pollOption['id'] . '</td>';
				echo '<td>' . $pollOption['name'] . '</td>';
				if (isset($isLoggedIn) && $isLoggedIn === true) {
					if (isset($vote)) {

						if ($vote['Vote']['poll_option_id'] === $pollOption['id']) {
							echo '<td> <span class="label label-success">' . $pollOption['vote_count'] . '</span></td>';
						} else {
							echo '<td> ' . $pollOption['vote_count'] . '</td>';
						}

					} else {
						echo '<td> <a href="/polls/vote/' . $pollOption['id'] . '" class="btn btn-large btn-primary">Vote</a></td>';
					}

				} else {
					echo '<td> </td>';
				}

				echo '</tr>';
			}
			?>
		</tbody>
	</table>
</div>