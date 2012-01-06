<?php
class SpecializedType extends AppModel {
	var $name = 'SpecializedType';
	var $useTable = 'specialized_types';
	var $hasMany = array('Collectible', 'CollectibletypesManufactureSpecializedType');
	var $actsAs = array('Containable');

}
?>