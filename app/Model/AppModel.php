<?php
/**
 * Application model for Cake.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       cake
 * @subpackage    cake.app
 */
App::uses('Model', 'Model');
App::uses('CakeEventListener', 'Event');
class AppModel extends Model {
	/**
	 * This loops through each individual return entry after the find so we can more eaily maniulate it.
	 */
	public function afterFind($results, $primary = false) {

		if (method_exists($this, 'doAfterFind')) {
			if ($primary) {
				foreach ($results as $key => $val) {
					if (isset($val[$this -> alias])) {
						$results[$key][$this -> alias] = $this -> doAfterFind($results[$key][$this -> alias], $primary);
					}
				}
			} else {
				if (isset($results[$this -> primaryKey])) {
					$results = $this -> doAfterFind($results);
				} else {
					foreach ($results as $key => $val) {
						if (isset($val[$this -> alias])) {
							if (isset($val[$this -> alias][$this -> primaryKey])) {
								$results[$key][$this -> alias] = $this -> doAfterFind($results[$key][$this -> alias], $primary);
							} else {
								foreach ($results[$key][$this->alias] as $key2 => $val2) {
									$results[$key][$this -> alias][$key2] = $this -> doAfterFind($results[$key][$this -> alias][$key2], $primary);
								}
							}
						}
					}
				}
			}
		}
		return $results;
	}

	/**
	 * Overriding paginateCount method to add in some extra code when doing paginate with a gorup by
	 *
	 * This will correct the incorrect count by the core code
	 */
	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$parameters = compact('conditions', 'recursive');

		if (isset($extra['group'])) {
			$parameters['fields'] = $extra['group'];

			if (is_string($parameters['fields'])) {
				// pagination with single GROUP BY field
				if (substr($parameters['fields'], 0, 9) != 'DISTINCT ') {
					$parameters['fields'] = 'DISTINCT ' . $parameters['fields'];
				}
				unset($extra['group']);
				$count = $this -> find('count', array_merge($parameters, $extra));
			} else {
				// resort to inefficient method for multiple GROUP BY fields
				$count = $this -> find('count', array_merge($parameters, $extra));
				$count = $this -> getAffectedRows();
			}
		} else {
			// regular pagination
			$count = $this -> find('count', array_merge($parameters, $extra));
		}
		return $count;
	}

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

	public function notifyUser($userEmail = null, $message) {
		$subscriptions = array();
		$subscription = array();
		$subscription['Subscription']['user_id'] = $userEmail;
		$subscription['Subscription']['message'] = $message;
		array_push($subscriptions, $subscription);

		CakeEventManager::instance() -> dispatch(new CakeEvent('Model.Subscription.notify', $this, array('subscriptions' => $subscriptions)));
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
