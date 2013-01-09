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

			if (isset($vote)) {

				if ($vote['Vote']['poll_option_id'] === $pollOption['id']) {
					echo '<td> <span class="label label-success">' . $pollOption['vote_count'] . '</span></td>';
				} else {
					echo '<td> ' . $pollOption['vote_count'] . '</td>';
				}

			} else {
				echo '<td> <a href="/polls/vote/' . $pollOption['id'] . '" class="btn btn-large btn-primary">Vote</a></td>';
			}

			echo '</tr>';
		}
		?>
	</tbody>
</table>