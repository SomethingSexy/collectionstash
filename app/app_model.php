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
class AppModel extends Model {
	public function afterFind($results, $primary =false) {
		if(method_exists($this, 'doAfterFind')) {
			if($primary) {
				foreach($results as $key => $val) {
					if(isset($val[$this -> alias])) {
						$results[$key][$this -> alias] = $this -> doAfterFind($results[$key][$this -> alias]);
					}
				}
			} else {
				if(isset($results[$this -> primaryKey])) {
					$results = $this -> doAfterFind($results);
				} else {
					foreach($results as $key => $val) {
						if(isset($val[$this -> alias])) {
							if(isset($val[$this -> alias][$this -> primaryKey])) {
								$results[$key][$this -> alias] = $this -> doAfterFind($results[$key][$this -> alias]);
							} else {
								foreach($results[$key][$this->alias] as $key2 => $val2) {
									$results[$key][$this -> alias][$key2] = $this -> doAfterFind($results[$key][$this -> alias][$key2]);
								}
							}
						}
					}
				}
			}
		}

		return $results;
	}

}
