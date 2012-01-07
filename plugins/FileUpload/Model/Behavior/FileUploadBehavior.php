<?php
/**
 * Behavior for file uploads
 *
 * Example Usage:
 *
 * @example
 *   var $actsAs = array('FileUpload.FileUpload');
 *
 * @example
 *   var $actsAs = array(
 *     'FileUpload.FileUpload' => array(
 *       'uploadDir'    => WEB_ROOT . DS . 'files',
 *       'fields'       => array('name' => 'file_name', 'type' => 'file_type', 'size' => 'file_size'),
 *       'allowedTypes' => array('pdf' => array('application/pdf')),
 *       'required'    => false,
 *       'unique' => false //filenames will overwrite existing files of the same name. (default true)
 *       'fileNameFunction' => 'sha1' //execute the Sha1 function on a filename before saving it (default false)
 *     )
 *    )
 *
 *
 * @note: Please review the plugins/file_upload/config/file_upload_settings.php file for details on each setting.
 * @version: since 6.1.0
 * @author: Nick Baker
 * @link: http://www.webtechnick.com
 */
require_once(dirname(__FILE__) . DS . '..' . DS . '..' . DS . 'Vendor' . DS . 'Uploader.php');
App::uses('Uploader', 'Vendor');
require_once(dirname(__FILE__) . DS . '..' . DS . '..' . DS . 'Config' . DS . 'FileUploadSettings.php');
App::uses('FileUploadSettings', 'Config');
class FileUploadBehavior extends ModelBehavior {

	/**
	 * Uploader is the uploader instance of class Uploader. This will handle the actual file saving.
	 */
	var $Uploader = array();

	function setFileUploadOption(&$Model, $key, $value) {
		$this -> options[$Model -> alias][$key] = $value;
		$this -> Uploader[$Model -> alias] -> setOption($key, $value);
	}

	/**
	 * Setup the behavior
	 */
	function setUp(&$Model, $options = array()) {
		$FileUploadSettings = new FileUploadSettings();
		if (!is_array($options)) {
			$options = array();
		}
		$this -> options[$Model -> alias] = array_merge($FileUploadSettings -> defaults, $options);

		$uploader_settings = $this -> options[$Model -> alias];
		$uploader_settings['uploadDir'] = $this -> options[$Model -> alias]['forceWebroot'] ? WWW_ROOT . $uploader_settings['uploadDir'] : $uploader_settings['uploadDir'];
		$uploader_settings['fileModel'] = $Model -> alias;
		$this -> Uploader[$Model -> alias] = new Uploader($uploader_settings);
	}

	/**
	 * beforeSave if a file is found, upload it, and then save the filename according to the settings
	 *
	 */
	function beforeSave(&$Model) {
		//debug($Model);
		$url = false;

		if (!empty($Model -> data[$Model -> alias]['url'])) {
			$url = $Model -> data[$Model -> alias]['url'];
			$urlFile = basename($url);
			$ch = curl_init($url);
			//$tmpDir = ini_get('upload_tmp_dir');
			$tmpDir = sys_get_temp_dir();
			debug(PATH_SEPARATOR);
			//$this->log($tmpDir, 'error');
			$fp = fopen($tmpDir . DIRECTORY_SEPARATOR . $urlFile, 'wb');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_exec($ch);
			$info = curl_getinfo($ch);
			debug($info);
			curl_close($ch);
			fclose($fp);
			$Model -> data[$Model -> alias][$this -> options[$Model -> alias]['fileVar']][$this -> options[$Model -> alias]['fields']['name']] = $urlFile;
			$Model -> data[$Model -> alias][$this -> options[$Model -> alias]['fileVar']][$this -> options[$Model -> alias]['fields']['size']] = $info['download_content_length'];
			$Model -> data[$Model -> alias][$this -> options[$Model -> alias]['fileVar']][$this -> options[$Model -> alias]['fields']['type']] = $info['content_type'];
			$Model -> data[$Model -> alias][$this -> options[$Model -> alias]['fileVar']]['tmp_name'] = $tmpDir . DIRECTORY_SEPARATOR . $urlFile;
			$Model -> data[$Model -> alias][$this -> options[$Model -> alias]['fileVar']]['error'] = '0';

			$url = true;
		}

		//This checks to see if the file array is set in the data that is passed in.
		if (isset($Model -> data[$Model -> alias][$this -> options[$Model -> alias]['fileVar']])) {
			//Here we are setting the $file variable to the data was that was passed into this model
			$file = $Model -> data[$Model -> alias][$this -> options[$Model -> alias]['fileVar']];

			//Set the file, here I believe this is needed to properly run hasUpload
			$this -> Uploader[$Model -> alias] -> file = $file;

			//This checks to see if there is an upload that was done
			if ($this -> Uploader[$Model -> alias] -> hasUpload()) {
				//If there is, then process that upload
				if ($url) {
					$fileName = $this -> Uploader[$Model -> alias] -> processURLFile();
				} else {
					$fileName = $this -> Uploader[$Model -> alias] -> processFile();
				}

				debug($fileName);
				if ($fileName) {
					$Model -> data[$Model -> alias][$this -> options[$Model -> alias]['fields']['name']] = $fileName;
					$Model -> data[$Model -> alias][$this -> options[$Model -> alias]['fields']['size']] = $file['size'];
					$Model -> data[$Model -> alias][$this -> options[$Model -> alias]['fields']['type']] = $file['type'];
				} else {
					$this -> log('Couldnt save', 'info');
					return false;
					// we couldn't save the file, return false
				}
				unset($Model -> data[$Model -> alias][$this -> options[$Model -> alias]['fileVar']]);
			} else {
				unset($Model -> data[$Model -> alias]);
			}
		}
		debug($Model -> data);
		return $Model -> beforeSave();
	}

	/**
	 * Updates validation errors if there was an error uploading the file.
	 * presents the user the errors.
	 */
	function beforeValidate(&$Model) {

		if (!empty($Model -> data[$Model -> alias]['url'])) {

		} else if (isset($Model -> data[$Model -> alias][$this -> options[$Model -> alias]['fileVar']])) {
			$file = $Model -> data[$Model -> alias][$this -> options[$Model -> alias]['fileVar']];
			$this -> Uploader[$Model -> alias] -> file = $file;
			if ($this -> Uploader[$Model -> alias] -> hasUpload()) {
				if ($this -> Uploader[$Model -> alias] -> checkFile() && $this -> Uploader[$Model -> alias] -> checkType() && $this -> Uploader[$Model -> alias] -> checkSize()) {
					$Model -> beforeValidate();
				} else {
					$Model -> validationErrors[$this -> options[$Model -> alias]['fileVar']] = $this -> Uploader[$Model -> alias] -> showErrors();
				}
			} else {
				if (isset($this -> options[$Model -> alias]['required']) && $this -> options[$Model -> alias]['required']) {
					$Model -> validationErrors[$this -> options[$Model -> alias]['fileVar']] = 'Select file to upload';
				} else if (!empty($Model -> data[$Model -> alias][$this -> options[$Model -> alias]['fileVar']][$this -> options[$Model -> alias]['fields']['name']])) {
					$Model -> validationErrors[$this -> options[$Model -> alias]['fileVar']] = 'Please verify the size and type of the image.';
				}
			}
		} elseif (isset($this -> options[$Model -> alias]['required']) && $this -> options[$Model -> alias]['required']) {
			$Model -> validationErrors[$this -> options[$Model -> alias]['fileVar']] = 'No File';
		}

		return $Model -> beforeValidate();
	}

	/**
	 * Automatically remove the uploaded file.
	 */
	function beforeDelete(&$Model, $cascade) {
		$Model -> recursive = -1;
		$data = $Model -> read();

		$this -> Uploader[$Model -> alias] -> removeFile($data[$Model -> alias][$this -> options[$Model -> alias]['fields']['name']]);
		return $Model -> beforeDelete($cascade);
	}

}
?>