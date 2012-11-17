<?php
/**
 * This shell converts attributes from the old model (pre 1.5) to the new model (1.5)
 *
 *
 * TODO: The converter needs to get updated, when the "Features" table is done
 * to add the attributes that are not items to the features table
 *
 * TODO: The converter needs to delete all of the inactive ones
 */
class AttributesConverterShell extends AppShell {
	public $uses = array('Attribute', 'AttributesCollectible');

	public function main() {
		//Grabbing them all for now that active
		$attributes = $this -> AttributesCollectible -> find("all", array('conditions' => array('active' => 1)));

		$featureAttributeIds = array(2, 4, 20, 3);
		foreach ($attributes as $key => $value) {

			// If it not a feature
			if (!in_array($value['AttributesCollectible']['attribute_id'], $featureAttributeIds) && !empty($value['Collectible']['id']) && !is_null($value['Collectible']['id'])) {
				$attribute = array();
				// These are all getting new revisions because they are now new indepdent items
				$attribute['Revision']['action'] = 'A';
				$attribute['Revision']['user_id'] = $value['Collectible']['user_id'];
				$attribute['Attribute']['attribute_category_id'] = $value['AttributesCollectible']['attribute_id'];
				// What should I default this to?
				$attribute['Attribute']['name'] = '';
				$attribute['Attribute']['description'] = $value['AttributesCollectible']['description'];
				$attribute['Attribute']['manufacture_id'] = $value['Collectible']['manufacture_id'];
				// Might have certain attributes this does not pertain to
				$attribute['Attribute']['scale_id'] = $value['Collectible']['scale_id'];
				// Set the created attribute as the one who added the collectible
				$attribute['Attribute']['user_id'] = $value['Collectible']['user_id'];
				$attribute['Attribute']['active'] = 1;
				$this -> Attribute -> create();
				if ($this -> Attribute -> saveAll($attribute, array('validate' => false))) {
					$id = $this -> Attribute -> id;
					$value['AttributesCollectible']['attribute_id'] = $id;
					$this -> AttributesCollectible -> save($value);
				} else {
					debug($this -> Attribute -> validationErrors);
				}
			} else {
				if (empty($value['Collectible']['id']) || is_null($value['Collectible']['id'])) {
					$this -> AttributesCollectible -> delete($value['AttributesCollectible']['id']);
				}
			}
		}

	}

}
?>