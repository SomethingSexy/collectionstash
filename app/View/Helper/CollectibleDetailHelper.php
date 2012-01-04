<?php

class CollectibleDetailHelper extends AppHelper {

	/**
	 * @param string $fieldDetail - model, field
	 * @param string $label -
	 * @param string $options - value, post value, preValue, link, compare, replace
	 */
	function field($details = null, $fieldDetail = array(), $label = '', $options = array()) {
		$model = $fieldDetail['Model'];
		$field = $fieldDetail['Field'];
		$isOverrideValue = false;
		$overrideValue = '';
		if(isset($options['value'])){
			$isOverrideValue = true;
			$overrideValue = $options['value'];
		}

		$output = '';
		if(isset($details[$model])){
			if(isset($details[$model][$field]) && !empty($details[$model][$field])){
				$output .= '<dt>';
				$output .= $label;
				$output .= '</dt>';
				if(isset($options['compare']) && $options['compare']){
					if(isset($details[$model][$field.'_changed']) && $details[$model][$field.'_changed']){
						$output .= '<dd class="changed">';
					} else {
						$output .= '<dd>';
					}	
				} else {
					$output .= '<dd>';	
				}
				
				
				if($isOverrideValue){
					$output .= $overrideValue;
				} else {
					if(isset($options['preValue'])){
						$output .= $options['preValue'];	
					}	
					$value = $details[$model][$field];		
					$value = str_replace('\n', "\n", $value);
					$value = str_replace('\r', "\r", $value);
					$value = nl2br($value);	
					$output .= $value;	
					if(isset($options['postValue'])){
						$output .= $options['postValue'];	
					}	
				}
				$output .= '</dd>';
			}
		}
		
		return $output;

	}

	// <?php
	// if($showCompareFields && isset($collectibleCore['Collectible']['manufacture_id_changed']) && $collectibleCore['Collectible']['manufacture_id_changed']){
	// echo '<dd class="changed">';
	// } else {
	// echo '<dd>';
	// }
	//
	// <a target="_blank" href="<?php echo $collectibleCore['Manufacture']['url'];">
	// $collectibleCore['Manufacture']['title'];
	// </a>
	// </dd>
}
?>
