<?php
/**
 * This shell converts uploads from the old model (pre 1.5) to the new model (1.5)
 *
 *
 */
class UploadsConverterShell extends AppShell {
	public $uses = array('Upload', 'CollectiblesUpload', 'Collectible');

	public function main() {
		$uploads = $this -> Upload -> find("all", array('contain' => array('Collectible')));

		foreach ($uploads as $key => $upload) {
			if (isset($upload['Upload']['collectible_id']) && !empty($upload['Upload']['collectible_id'])) {
				$collectiblesUpload = array();
				$collectiblesUpload['Revision']['user_id'] = $upload['Collectible']['user_id'];
				$collectiblesUpload['Revision']['action'] = 'A';
				$collectiblesUpload['CollectiblesUpload']['upload_id'] = $upload['Upload']['id'];
				$collectiblesUpload['CollectiblesUpload']['collectible_id'] = $upload['Upload']['collectible_id'];
				$this -> CollectiblesUpload -> create();
				if ($this -> CollectiblesUpload -> saveAll($collectiblesUpload)) {
					$this -> Upload -> id = $upload['Upload']['id'];
					$this -> Upload -> saveField('user_id', $upload['Collectible']['user_id'], false);
				}

			} else {
				$this -> Upload -> delete($upload['Upload']['id']);
			}
		}	}

}
?>