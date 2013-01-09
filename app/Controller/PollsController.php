<?php
App::uses('Sanitize', 'Utility');
class PollsController extends AppController {

	public $helpers = array('Html', 'Minify');

	public function index() {

		// check to see if they have voted in this poll already
		$vote = $this -> Poll -> Vote -> find('first', array('conditions' => array('Vote.user_id' => $this -> getUserId(), 'Vote.poll_id' => 1)));

		if (empty($vote)) {
			// did not vote
			// get the poll
			$poll = $this -> Poll -> find('first', array('conditions' => array('Poll.id' => 1)));

			debug($poll);
			$this -> set(compact('poll'));
		} else {
			// get the poll
			$poll = $this -> Poll -> find('first', array('contain' => array('PollOption' => array('order' => array('PollOption.vote_count' => 'DESC'))), 'conditions' => array('Poll.id' => 1)));

			debug($poll);
			$this -> set(compact('poll'));
			// voted already
			$this -> set(compact('vote'));
		}

	}

	public function vote($pollOptionId) {
		// needs to be logged in
		// needs to be valid poll
		// can only vote once

		$vote = array();
		$vote['Vote']['poll_option_id'] = $pollOptionId;
		$vote['Vote']['poll_id'] = 1;
		$vote['Vote']['user_id'] = $this -> getUserId();

		$this -> Poll -> Vote -> saveAll($vote);

		$this -> redirect(array('action' => 'index'));
	}

}
?>
