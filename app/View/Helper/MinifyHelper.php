<?php
/***
 * Cakephp view helper to interface with http://code.google.com/p/minify/ project.
 * Minify: Combines, minifies, and caches JavaScript and CSS files on demand to speed up page loads.
 * @author: Ketan Shah - ketan.shah@gmail.com - http://www.innovatechnologies.in
 * Requirements: An entry in core.php - "MinifyAsset" - value of which is either set 'true' or 'false'. False would be usually set during development and/or debugging. True should be set in production mode.
 */

class MinifyHelper extends AppHelper {

	var $helpers = array('Js', 'Html');
	//used for seamless degradation when MinifyAsset is set to false;

	function script($url, $options = array()) {
		return $this -> Html -> script(Configure::read('MinifyAsset') ? $this -> path($url, 'js') : $url, $options);
	}

	public function css($path, $rel = null, $options = array()) {
		return $this -> Html -> css(Configure::read('MinifyAsset') ? $this -> path($path, 'css') : $path, $rel, $options);
	}

	function path($assets, $ext) {
		if (!is_array($assets)) {
			$assets = array($assets);
		}
		$path = $this -> webroot ."min/?f=";

		if ($ext === 'js') {
			$path .= 'js/';
		} else if ($ext === 'css') {
			$path .= 'css/';
		}

		foreach ($assets as $asset) {
			$path .= ($asset . ".$ext,");
		}
		return substr($path, 0, count($path) - 2);
	}

}
?>
