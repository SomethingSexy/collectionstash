<?php
/**
 * This will be a one time shell that will calcualte all existing points for a user
 *
 */
class TotalExistingPointsShell extends AppShell {
	public $uses = array('User', 'CollectiblesUser', 'Comment', 'UserUpload', 'Collectible', 'Attribute', 'Point');

	public function main() {

		// Just grab all of the users and I will do any manually processing, should be faster
		$users = $this -> User -> find("all", array('contain' => false));

		$points = $this -> Point -> find('all');

		$processedPoints = array();
		foreach ($points as $key => $value) {
			$processedPoints[$value['Point']['activity_type_id']] = $value['Point']['points'];
		}

		// Even though the User table should be accurate I am going to manually get counts just to be safe
		foreach ($users as $key => $user) {
			$totalPoints = 0;
			// Count all current collectibles in their stash and wishlist
			$totalUserCollectibles = $this -> CollectiblesUser -> find("count", array('conditions' => array('CollectiblesUser.user_id' => $user['User']['id'])));

			// Count all comments
			$totalComments = $this -> Comment -> find("count", array('conditions' => array('Comment.user_id' => $user['User']['id'])));

			// Count all photos
			$totalUploads = $this -> UserUpload -> find("count", array('conditions' => array('UserUpload.user_id' => $user['User']['id'])));

			// Count all collectibles submitted that are in an active status
			$totalCollectibles = $this -> Collectible -> find("count", array('conditions' => array('Collectible.state' => 0, 'Collectible.user_id' => $user['User']['id'])));

			$totalAttributes = $this -> Attribute -> find("count", array('conditions' => array('Attribute.status_id' => 4, 'Attribute.user_id' => $user['User']['id'])));

			$totalInvites = $user['User']['invite_count'];

			// We can try this field, might work out for some people
			$totalEdits = $user['User']['edit_count'];

			// caculate points
			if (isset($processedPoints['2'])) {
				$totalPoints = $totalPoints + ($totalUserCollectibles * $processedPoints['2']);
			}

			if (isset($processedPoints['1'])) {
				$totalPoints = $totalPoints + ($totalComments * $processedPoints['1']);
			}

			if (isset($processedPoints['5'])) {
				$totalPoints = $totalPoints + ($totalUploads * $processedPoints['5']);
			}

			if (isset($processedPoints['8'])) {
				$totalPoints = $totalPoints + ($totalCollectibles * $processedPoints['8']);
				$totalPoints = $totalPoints + ($totalAttributes * $processedPoints['8']);
			}

			if (isset($processedPoints['10'])) {
				$totalPoints = $totalPoints + ($totalInvites * $processedPoints['10']);
			}

			if (isset($processedPoints['9'])) {
				$totalPoints = $totalPoints + ($totalEdits * $processedPoints['9']);
			}

			$this -> User -> id = $user['User']['id'];
			$this -> User -> saveField('points', $totalPoints, false);
			debug($totalPoints);
		}

		// Count all attributes submitted that are in an active status
	}

}
?>