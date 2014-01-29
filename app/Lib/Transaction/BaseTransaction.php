<?php
class BaseTransaction extends Object {
	public function convertErrorsJSON($errors = null, $model = null) {
		$retVal = array();

		if (!is_null($errors)) {
			foreach ($errors as $key => $value) {
				$error = array();
				if (!is_null($model)) {
					$error['model'] = $model;
				}
				$error['name'] = $key;
				$error['message'] = $value;
				$error['inline'] = true;
				array_push($retVal, $error);
			}
		}

		return $retVal;
	}

	public function buildDefaultResponse() {
		$retVal = array();
		$retVal['response'] = array();
		$retVal['response']['isSuccess'] = false;
		$retVal['response']['message'] = '';
		$retVal['response']['code'] = 0;
		$retVal['response']['data'] = array();
		//Maybe this should be an error code
		$retVal['response']['errors'] = array();

		return $retVal;
	}

}
?>
