<?php
/**
 * This is used to indicate that a model is an entity type
 *
 *  Might be able to update this to handle more stuff evenetually
 */
class EntityTypeBehavior extends ModelBehavior {

	/**
	 * Defaul setting values
	 *
	 * @access private
	 * @var array
	 */
	private $defaults = array();

	/**
	 * Configure the behavior through the Model::actsAs property
	 *
	 * @param object $Model
	 * @param array $config
	 */
	public function setup(&$Model, $settings = null) {
		if (is_array($settings)) {
			$this -> settings[$Model -> alias] = array_merge($this -> defaults, $settings);
		} else {
			$this -> settings[$Model -> alias] = $this -> defaults;
		}
	}

	function beforeSave(&$Model) {

		if (!$Model -> id && !isset($Model -> data[$Model -> alias][$Model -> primaryKey])) {
			// insert
			$type = strtolower($Model -> alias);
			$Model -> data['EntityType']['type'] = $type;
			debug($Model -> data);
		} else {
			// edit
		}
		return true;
	}

}
?>