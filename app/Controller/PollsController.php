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
		if ($this -> isLoggedIn()) {
			if (is_numeric($pollOptionId)) {

				$vote = $this -> Poll -> Vote -> find('first', array('conditions' => array('Vote.user_id' => $this -> getUserId(), 'Vote.poll_id' => 1)));
				if (empty($vote)) {
					$vote = array();
					$vote['Vote']['poll_option_id'] = $pollOptionId;
					$vote['Vote']['poll_id'] = 1;
					$vote['Vote']['user_id'] = $this -> getUserId();

					$this -> Poll -> Vote -> saveAll($vote);
				} else {
					$this -> Session -> setFlash(__('Nice try, you can only vote once!', true), null, null, 'error');
				}
			} else {
				$this -> Session -> setFlash(__('Invalid request.', true), null, null, 'error');
			}
		} else {
			$this -> Session -> setFlash(__('You must be logged in to vote.', true), null, null, 'error');
		}

		$this -> redirect(array('action' => 'index'));
	}

}
?>
