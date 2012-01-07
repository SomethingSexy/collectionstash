<?php
/**
 * Sluggable Behavior class
 *
 * Creates slugs-key of DB entries on-the-fly
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author Lucas Ferreira
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 * @copyright Copyright 2010-2011, Burn web.studio - http://www.burnweb.com.br/
 * @version 0.7b
 */

class SluggableBehavior extends ModelBehavior {
	var $options = array();

	function setup(&$model, $settings = array()) {
		$_options = array_merge(array('name' => $model -> alias, 'schema' => $model -> schema(), 'displayField' => array(), 'showPrimary' => true, 'primaryKey' => $model -> primaryKey, 'slugField' => 'slug', 'replacement' => '-'), $settings);

		$this -> options[$model -> alias] = &$_options;
	}

	function __slug($description = null, $id = null, $s = "-") {
		if (function_exists("_slug")) {
			return _slug($description, $id, $s);
		} else if (class_exists("Util")) {
			return Util::slug($description, $id, $s);
		} else {
			if ($id !== null) {
				$slugged = Inflector::slug(trim($description), $s) . "{$s}{$id}";
			} else {
				$slugged = Inflector::slug(trim($description), $s);
			}

			return function_exists("mb_strtolower") ? mb_strtolower($slugged) : strtolower($slugged);
		}
	}

	function beforeFind(&$model, $data = array()) {
		if (!empty($data['conditions'])) {
			$slug = null;
			$o = $this -> options[$model -> alias];
			$conditions = $data['conditions'];
			if (!empty($conditions["{$model->alias}.{$o['slugField']}"])) {
				$slug = $conditions["{$model->alias}.{$o['slugField']}"];
				unset($conditions["{$model->alias}.{$o['slugField']}"]);
			}
			if (!empty($conditions["{$o['slugField']}"])) {
				$slug = $conditions["{$o['slugField']}"];
				unset($conditions["{$o['slugField']}"]);
			}
			if (!empty($slug)) {
				$id = end(explode("-", $slug));
				$conditions["{$model->alias}.{$o['primaryKey']}"] = $id;
			}
			$data['conditions'] = $conditions;
		}

		return $data;
	}

	function afterFind(&$model, $data = array()) {
		//$d is the actual data being return
		foreach ($data as $i => $d) {
			if (is_array($this -> options[$model -> alias]['displayField'])) {
				$fields = $this -> options[$model -> alias]['displayField'];
				$slug = '';
				foreach ($fields as $key => $value) {
					if(isset($d[$value['Model']]) && isset($d[$value['Model']][$value['Field']])) {
						$slug .= $d[$value['Model']][$value['Field']] . ' ';
					}
				}
				if ($this -> options[$model -> alias]['showPrimary']) {
					$slug = $this -> __slug($slug, $d[$model -> alias][$this -> options[$model -> alias]['primaryKey']], $this -> options[$model -> alias]['replacement']);
				} else {
					$slug = $this -> __slug($slug, null, $this -> options[$model -> alias]['replacement']);
				}

				$d[$model -> alias]['slugField'] = $slug;
			}
			debug($d);
			$data[$i] = $d;
		}

		return $data;
	}

}
?>