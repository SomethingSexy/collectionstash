<?php
/**
 * This shell updates the attribute category path name to reflect the location of the path
 */
class BuildAttributeCategoryPathShell extends AppShell {
	public $uses = array('AttributeCategory');

	public function main() {
		//Grabbing them all for now
		$attributes = $this -> AttributeCategory -> find("all");
		foreach ($attributes as $key => $value) {
			$this -> AttributeCategory -> updatePath($value['AttributeCategory']['id']);
		}

	}

}
?>

