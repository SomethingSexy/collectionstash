<?php
App::uses('PhpThumbFactory','Vendor');
App::uses('RResizeImage', 'Lib');
/**
 * This is an image component that allows us to resize images from the controller.  This has duplicate code
 * to the fileupload view helper
 */
class ImageComponent extends Component {

	var $options = array('width' => 0, 'height' => 0, //0 means no resizing
		'resizedDir' => 'resized', // make sure webroot/files/resized is chmod 777
		'imagePathOnly' => false, //if true, will only return the requested image_path
		'autoResize' => true, //if true, will resize the file automatically if given a valid width.
		'resizeThumbOnly' => true //if true, will only resize the image down -- not up past the original's size
	);
	var $settings = array();

	/**
	 * image takes a file_name or Upload.id and returns the HTML image
	 *
	 * @param String|Int $name takes a file_name or ID of uploaded file.
	 * @param Array|Int $options takes an array of options passed to the image helper, or an integer representing the width of the image to display
	 *         options: width = 100 (default), if width is set along with autoResize the uploaded image will be resized.
	 * @access public
	 * @return mixed html tag, url string, or false if unable to find image.
	 */
	function image($name, $options = array()) {
		$this -> fileName = $name;
		//options takes in a width as well
		if (is_int($options)) {
			$width = $options;
			$options = array();
			$options['width'] = $width;
		}
		$this -> options = array_merge($this -> options, $options);
		$this -> settings = array_merge($this -> settings, $options);

		$img = false;
		if (is_string($name)) {
			$img = $this -> _getImageByName();
		} elseif (is_int($name)) {
			$img = $this -> _getImageById();
		}
		list($width, $height, $type, $attr) = getimagesize(WWW_ROOT . $this -> _htmlImage());
		if ($img) {
			
			$returnArray = array();
			$returnArray['path'] = $img;
			$returnArray['height'] = $height;
			return $returnArray;
		}

		$this -> log("Unable to find $img");
		return false;
	}

	/**
	 * input takes an array of options and display the file browser html input
	 * options.
	 * @param Array $options of model and file options.  Defaults to default FileUpload component configuration
	 * @return String HTML form input element configured for the FileUploadComponent
	 * @access public
	 */
	function input($options = array()) {
		$options = array_merge(array('var' => $this -> settings['fileVar'], 'model' => $this -> settings['fileModel']), $options);
		$configs = $options;
		if ($configs['model']) {
			unset($options['model'], $options['var']);

			return $this -> Form -> input("{$configs['model']}." . $this -> inputCount++ . ".{$configs['var']}", array_merge(array('type' => 'file'), $options));
		} else {
			return "<input type='file' name='data[{$configs['var']}][" . $this -> inputCount++ . "]' />";
		}
	}

	/**
	 * @access protected
	 */
	function _getImageById() {
		App::import('Component', 'FileUpload.FileUpload');
		$this -> FileUpload = new FileUploadComponent;

		$id = $this -> fileName;
		$this -> FileUpload -> options['fileModel'] = $this -> settings['fileModel'];
		$Model = &$this -> FileUpload -> getModel();
		$Model -> recursive = -1;
		$upload = $Model -> findById($id);
		if (!empty($upload)) {
			$this -> fileName = $upload[$this -> settings['fileModel']][$this -> settings['fields']['name']];
			return $this -> _getImageByName();
		} else {
			return false;
		}
	}

	/**
	 * _getFullPath returns the full path of the file name
	 * @access protected
	 * @return String full path of the file name
	 */
	function _getFullPath() {
		if ($this -> _isOutsideSource()) {
			return $this -> fileName;
		} else {
			return WWW_ROOT . $this -> _getUploadPath();
		}
	}

	/**
	 * _getImagePath returns the image path of the file name
	 * @access protected
	 * @return String full path of the file name
	 */
	function _getImagePath() {
		if ($this -> _isOutsideSource()) {
			return $this -> fileName;
		} else {
			return '/' . $this -> _getUploadPath();
		}
	}

	/**
	 * _getUploadPath returns the upload path of all files
	 * @access protected
	 * @return String upload path of all files
	 */
	function _getUploadPath() {
		return $this -> settings['uploadDir'] . '/' . $this -> fileName;
	}

	/**
	 * _getExt returns the extension of the filename.
	 * @access protected
	 * @return String extension of filename
	 */
	function _getExt() {
		return strrchr($this -> fileName, ".");
	}

	/**
	 * Get the image by name and width.
	 * if width is not specified return full image
	 * if width is specified, see if width of image exists
	 * if not, make it, save it, and return it.
	 * @return String HTML of resized or full image.
	 */
	function _getImageByName() {
		//only proceed if we actually have the file in question
		if (!$this -> _isOutsideSource() && !file_exists($this -> _getFullPath()))
			return false;
		//resize if we have resize on, a width, and if it doesn't already exist.
		if ($this -> options['autoResize'] && $this -> options['width'] > 0 && !file_exists($this -> _getResizeNameOrPath($this -> _getFullPath()))) {
			$this -> _resizeImage();
		}
		return $this -> _htmlImage();
	}

	/**
	 * @return String of the resizedpath of a filename or path.
	 * @access protected
	 */
	function _getResizeNameOrPath($file_name_or_path) {
		$file_name = basename($file_name_or_path);
		$path = substr($file_name_or_path, 0, strlen($file_name_or_path) - strlen($file_name));
		$temp_path = substr($file_name, 0, strlen($file_name) - strlen($this -> _getExt())) . "x" . $this -> options['width'] . $this -> _getExt();
		$full_path = (strlen($this -> options['resizedDir']) > 0) ? $path . $this -> options['resizedDir'] . '/' . $temp_path : $path . $temp_path;
		return $full_path;
	}

	/**
	 * _resizeImage actually resizes the passed in image.
	 * @access protected
	 * @return null
	 */
	function _resizeImage() {
		$this -> newImage = new RResizeImage($this -> _getFullPath());
		if ($this -> newImage -> imgWidth > $this -> options['width']) {
			//$this -> newImage -> resize_limitwh($this -> options['width'], 0, $this -> _getResizeNameOrPath($this -> _getFullPath()));
			$this -> newImage -> resize($this -> options['width'], $this -> options['height'], $this -> _getResizeNameOrPath($this -> _getFullPath()));

		} else {
			//$this->autoResize = false;
		}
	}

	/**
	 * _htmlImage returns the atual HTML of the resized/full image asked for
	 * @access protected
	 * @return String HTML image asked for
	 */
	function _htmlImage() {
		if (!$this -> _isOutsideSource() && $this -> options['autoResize'] && $this -> options['width'] > 0) {
			if (isset($this -> newImage) && $this -> newImage -> imgWidth && $this -> newImage -> imgWidth <= $this -> options['width']) {
				$image = $this -> _getImagePath();
			} else {
				$image = $this -> _getResizeNameOrPath($this -> _getImagePath());
			}
		} else {
			$image = $this -> _getImagePath();
		}

		$options = $this -> options;
		//copy
		//unset the default options
		unset($options['resizedDir'], $options['uploadDir'], $options['imagePathOnly'], $options['autoResize'], $options['resizeThumbOnly']);
		//unset width only if we're not an outsourced image, we have resize turned on, or we don't have a width to begin with.
		if (!$this -> _isOutsideSource() && ($this -> options['resizeThumbOnly'] || !$options['width'])) {
			unset($options['width']);
			unset($options['height']);
		}

		//return the impage path or image html
		if ($this -> options['imagePathOnly']) {
			return $image;
		} else {
			unset($this -> newImage);
			return $image;
		}
	}

	/**
	 * _isOutsideSource searches the fileName string for :// to determine if the image source is inside or outside our server
	 */
	function _isOutsideSource() {
		return !!strpos($this -> fileName, '://');
	}

}
?>
