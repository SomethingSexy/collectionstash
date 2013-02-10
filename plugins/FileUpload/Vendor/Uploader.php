<?php
/**
 * Uploader class handles a single file to be uploaded to the file system
 *
 * @author: Nick Baker
 * @version: since 6.0.0
 * @link: http://www.webtechnick.com
 */
class Uploader {

	/**
	 * File to upload.
	 */
	var $file = array();

	/**
	 * Global options
	 * fileTypes to allow to upload
	 */
	var $options = array();

	/**
	 * errors holds any errors that occur as string values.
	 * this can be access to debug the FileUploadComponent
	 *
	 * @var array
	 * @access public
	 */
	var $errors = array();

	/**
	 * Definitions of errors that could occur during upload
	 *
	 * @author Jon Langevin
	 * @var array
	 */
	var $uploadErrors = array(UPLOAD_ERR_OK => 'There is no error, the file uploaded with success.', UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.', UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.', UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.', UPLOAD_ERR_NO_FILE => 'No file was uploaded.', UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.', //Introduced in PHP 4.3.10 and PHP 5.0.3.
		UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.', //Introduced in PHP 5.1.0.
		UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.' //Introduced in PHP 5.2.0.
	);

	/**
	 * Final file is set on move_uploaded_file success.
	 * This is the file name of the final file that was uploaded
	 * to the uploadDir directory
	 *
	 * @var string of final file name uploaded
	 * @access public
	 */
	var $finalFile = null;

	function __construct($options = array()) {
		$this -> options = array_merge($this -> options, $options);
	}

	function setOption($key, $value) {
		$this -> options[$key] = $value;
	}

	/**
	 * Preform requested callbacks on the filename.
	 *
	 * @var string chosen filename
	 * @return string of resulting filename
	 * @access private
	 */
	function __handleFileNameCallback($fileName) {
		if ($this -> options['fileNameFunction']) {
			if ($this -> options['fileModel']) {
				$Model = ClassRegistry::init($this -> options['fileModel']);
				if (method_exists($Model, $this -> options['fileNameFunction'])) {
					$fileName = $Model -> {$this->options['fileNameFunction']}($fileName);
				} elseif (function_exists($this -> options['fileNameFunction'])) {
					$fileName = call_user_func($this -> options['fileNameFunction'], $fileName);
				}
			} else {
				if (function_exists($this -> options['fileNameFunction'])) {
					$fileName = call_user_func($this -> options['fileNameFunction'], $fileName);
				}
			}

			if (!$fileName) {
				$this -> _error('No filename resulting after parsing. Function: ' . $this -> options['fileNameFunction']);
			}
		}
		debug($fileName);
		return $fileName;
	}

	/**
	 * Preform requested callbacks on the upload director
	 *
	 * @var string chosen upload directory
	 * @return string of resulting upload directory
	 * @access private
	 */
	function __handleUploadDirCallback($uploadDirectory) {
		if ($this -> options['uploadDirFunction']) {
			if ($this -> options['fileModel']) {
				$Model = ClassRegistry::init($this -> options['fileModel']);
				if (method_exists($Model, $this -> options['uploadDirFunction'])) {
					$uploadDirectory = $Model -> {$this->options['uploadDirFunction']}($uploadDirectory);
				} elseif (function_exists($this -> options['uploadDirFunction'])) {
					$uploadDirectory = call_user_func($this -> options['uploadDirFunction'], $uploadDirectory);
				}
			} else {
				if (function_exists($this -> options['uploadDirFunction'])) {
					$uploadDirectory = call_user_func($this -> options['uploadDirFunction'], $uploadDirectory);
				}
			}

			if (!$uploadDirectory) {
				$this -> _error('No filename resulting after parsing. Function: ' . $this -> options['uploadDirFunction']);
			}
		}
		debug($uploadDirectory);
		return $uploadDirectory;
	}

	/**
	 * Preform requested target patch checks depending on the unique setting
	 *
	 * @var string chosen filename target_path
	 * @return string of resulting target_path
	 * @access private
	 */
	function __handleUnique($fileName) {
		if ($this -> options['unique']) {
			//If it is set to make unique, now we are create a UUID to make this file unique
			debug($fileName);

			if ($this -> options['randomFileName']) {
				$fileName = String::uuid() . $this -> _ext();
				debug($fileName);
			} else {
				//TODO fix this and put it back in case I want to revert back
				// $temp_path = substr($fileName, 0, strlen($fileName) - strlen($this -> _ext()));
				// //temp path without the ext
				// $i = 1;
				// while (file_exists($target_path)) {
				// $target_path = $temp_path . "-" . $i . $this -> _ext();
				// $i++;
				// }
			}

		}
		return $fileName;
	}

	/**
	 * processFile will take a file, or use the current file given to it
	 * and attempt to save the file to the file system.
	 * processFile will check to make sure the file is there, and its type is allowed to be saved.
	 *
	 * @param file array of uploaded file (optional)
	 * @return String | false String of finalFile name saved to the file system or false if unable to save to file system.
	 * @access public
	 */
	function processFile($file = null) {
		$this -> setFile($file);
		//check if we have a file and if we allow the type, return false otherwise.
		if (!$this -> checkFile() || !$this -> checkType() || !$this -> checkSize()) {
			return false;
		}

		//make sure the file doesn't already exist, if it does, add an itteration to it
		$up_dir = $this -> options['uploadDir'];
		$up_dir = $this -> __handleUploadDirCallback($up_dir);
		debug($up_dir);
		if (!is_dir($up_dir)) {
			mkdir($up_dir, 0777, true);
		}

		if (!is_dir($up_dir . DS . 'resized')) {
			mkdir($up_dir . DS . 'resized', 0777, true);
		}

		debug($this -> file['name']);
		//Check for any updates with the file name from a call back
		$fileName = $this -> __handleFileNameCallback($this -> file['name']);

		//if callback returns false hault the upload
		if (!$fileName) {
			return false;
		}
		//Update the file name if it needs to be unique
		$fileName = $this -> __handleUnique($fileName);
		$target_path = $up_dir . DS . $fileName;
		debug($target_path);
		debug($this -> file['tmp_name']);
		//now move the file.
		if (move_uploaded_file($this -> file['tmp_name'], $target_path)) {
			$this -> finalFile = basename($target_path);

			return $this -> finalFile;
		} else {
			$this -> _error('Unable to save temp file to file system.');
			CakeLog::write('error', 'Unable to move uploaded temp file ' . $this -> file['name'] . ' ' . $this -> file['tmp_name'] . ' ' . $this -> file['error']);
			return false;
		}
	}

	function processURLFile() {
		//check if we have a file and if we allow the type, return false otherwise.
		if (!$this -> checkFile() || !$this -> checkType() || !$this -> checkSize()) {
			return false;
		}

		//make sure the file doesn't already exist, if it does, add an itteration to it
		$up_dir = $this -> options['uploadDir'];
		//Check for any updates with the file name from a call back
		$fileName = $this -> __handleFileNameCallback($this -> file['name']);

		//if callback returns false hault the upload
		if (!$fileName) {
			return false;
		}
		//Update the file name if it needs to be unique
		$fileName = $this -> __handleUnique($fileName);
		$target_path = $up_dir . DS . $fileName;
		debug($target_path);
		debug($this -> file['tmp_name']);
		//now move the file.
		if (copy($this -> file['tmp_name'], $target_path)) {
			unlink($this -> file['tmp_name']);
			$this -> finalFile = basename($target_path);
			return $this -> finalFile;
		} else {
			$this -> _error('Unable to save temp file to file system.');
			CakeLog::write('error', 'Unable to move uploaded temp file ' . $this -> file['name'] . ' ' . $this -> file['tmp_name'] . ' ' . $this -> file['error']);
			return false;
		}

	}

	/**
	 * setFile will set a this->file if given one.
	 *
	 * @param file array of uploaded file. (optional)
	 * @return void
	 */
	function setFile($file = null) {
		if ($file)
			$this -> file = $file;
	}

	/**
	 * Returns the extension of the uploaded filename.
	 *
	 * @return string $extension A filename extension
	 * @param file array of uploaded file (optional)
	 * @access protected
	 */
	function _ext($file = null) {
		$this -> setFile($file);
		debug($this -> file['name']);
		return strrchr($this -> file['name'], ".");
	}

	/**
	 * Adds error messages to the component
	 *
	 * @param string $text String of error message to save
	 * @return void
	 * @access protected
	 */
	function _error($text) {
		$this -> errors[] = __($text, true);
		debug($this -> errors);
	}

	/**
	 * Checks if the uploaded type is allowed defined in the allowedTypes
	 *
	 * @return boolean if type is accepted
	 * @param file array of uploaded file (optional)
	 * @access public
	 */
	function checkType($file = null) {
		$this -> setFile($file);
		foreach ($this->options['allowedTypes'] as $ext => $types) {
			if (!is_string($ext)) {
				$ext = $types;
			}
			if ($ext == '*') {
				return true;
			}

			$ext = strtolower('.' . str_replace('.', '', $ext));
			$file_ext = strtolower($this -> _ext());
			debug($ext);
			debug($file_ext);
			if ($file_ext == $ext) {
				if (is_array($types) && !in_array($this -> file['type'], $types)) {
					$this -> _error("{$this->file['type']} is not an allowed type.");
					CakeLog::write('error', 'The file type of the uploaded file ' . $this -> file['name'] . 'is not allowed' . $this -> file['type']);
					return false;
				} else {
					return true;
				}
			}
		}
		CakeLog::write('error', 'The file ' . $this -> file['name'] . ' extension is not allowed.' . $this -> file['error']);
		$this -> _error("{$this->file['type']} is not an allowed type.");
		return false;
	}

	/**
	 * Checks if there is a file uploaded
	 *
	 * @return void
	 * @access public
	 * @param file array of uploaded file (optional)
	 */
	function checkFile($file = null) {
		$this -> setFile($file);
		if ($this -> hasUpload() && $this -> file) {
			if (isset($this -> file['error']) && $this -> file['error'] == UPLOAD_ERR_OK) {
				return true;
			} else {
				$this -> _error($this -> uploadErrors[$this -> file['error']]);
			}
		}
		CakeLog::write('error', 'The file ' . $this -> file['name'] . ' did not upload correctly ' . $this -> file['error']);
		return false;
	}

	/**
	 * Checks if the file uploaded exceeds the maxFileSize setting (if there is onw)
	 *
	 * @return boolean
	 * @access public
	 * @param file array of uploaded file (optional)
	 */
	function checkSize($file = null) {
		$this -> setFile($file);
		if ($this -> hasUpload() && $this -> file) {
			if (!$this -> options['maxFileSize']) {//We don't want to test maxFileSize
				return true;
			} elseif ($this -> options['maxFileSize'] && $this -> file['size'] < $this -> options['maxFileSize']) {

				return true;
			} else {

				$this -> _error("File exceeds {$this->options['maxFileSize']} byte limit.");
			}
		}
		CakeLog::write('error', 'The file ' . $this -> file['name'] . ' did not upload because the file size it too big');
		return false;
	}

	/**
	 * removeFile removes a specific file from the uploaded directory
	 *
	 * @param string $name A reference to the filename to delete from the uploadDirectory
	 * @return boolean
	 * @access public
	 */
	function removeFile($name = null) {
		if (!$name || strpos($name, '://')) {
			return false;
		}

		$up_dir = $this -> options['uploadDir'];
		$up_dir = $this -> __handleUploadDirCallback($up_dir);
		$target_path = $up_dir . DS . $name;

		//delete main image -- $name
		if (@unlink($target_path)) {
			//If we delete the main image, find any resized and delete
			$tempName = substr($name, 0, strrpos($name, '.'));
			$files = glob($up_dir . DS . 'resized' . DS . $tempName . '*');
			foreach ($files as $key => $value) {
				//delete each one of these, if for some reason they do not delete, ignore we will use a script to clean up old files
				@unlink($value);
			}

			return true;
		} else {
			return false;
		}
	}

	/**
	 * hasUpload
	 *
	 * @return boolean true | false depending if a file was actually uploaded.
	 * @param file array of uploaded file (optional)
	 */
	function hasUpload($file = null) {
		$this -> setFile($file);
		return ($this -> _multiArrayKeyExists("tmp_name", $this -> file));
	}

	/**
	 * @return boolean true if errors were detected.
	 */
	function hasErrors() {
		return count($this -> errors);
	}

	/**
	 * showErrors itterates through the errors array
	 * and returns a concatinated string of errors sepearated by
	 * the $sep
	 *
	 * @param string $sep A seperated defaults to <br />
	 * @return string
	 * @access public
	 */
	function showErrors($sep = " ") {
		$retval = "";
		foreach ($this->errors as $error) {
			$retval .= "$error $sep";
		}
		return $retval;
	}

	/**
	 * Searches through the $haystack for a $key.
	 *
	 * @param string $needle String of key to search for in $haystack
	 * @param array $haystack Array of which to search for $needle
	 * @return boolean true if given key is in an array
	 * @access protected
	 */
	function _multiArrayKeyExists($needle, $haystack) {
		if (is_array($haystack)) {
			foreach ($haystack as $key => $value) {
				if ($needle === $key && $value) {
					return true;
				}
				if (is_array($value)) {
					if ($this -> _multiArrayKeyExists($needle, $value)) {
						return true;
					}
				}
			}
		}
		return false;
	}

}
?>