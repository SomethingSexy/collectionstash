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
		if (isset($options['value'])) {
			$isOverrideValue = true;
			$overrideValue = $options['value'];
		}

		$output = '';
	
		if (isset($details[$model])) {
			//TODO: Putting this back to empty, because it hides the false values
			// I originally had it checking !== '', but that was incorrectly
			// showing fields....this needs to get revamped
			if (isset($details[$model][$field]) && !empty($details[$model][$field])) {
				
				$output .= '<dt>';
				$output .= $label;
				$output .= '</dt>';

				// calculate the value, here because we are going to
				// add it as a data property to get later
				// doing this because some of the actual values
				// might be masked by display values...think ids
				$value = $details[$model][$field];
				$value = str_replace('\n', "\n", $value);
				$value = str_replace('\r', "\r", $value);
				$value = nl2br($value);
				$vaule = html_entity_decode($value); 

				$valueAttr = 'data-value="' . $value . '"';

				$class = '';
				if (isset($options['class']) && $options['class']) {
					$class = $options['class'];
				}

				if (isset($options['compare']) && $options['compare']) {
					if (isset($details[$model][$field . '_changed']) && $details[$model][$field . '_changed']) {
						$output .= '<dd class="data-value changed ' . $class . '" ' . $valueAttr . '>';
					} else {
						$output .= '<dd class="data-value ' . $class . '" ' . $valueAttr . '>';
					}
				} else {
					$output .= '<dd class="data-value ' . $class . '" ' . $valueAttr . '>';
				}

				if ($isOverrideValue) {
					$output .= $overrideValue;
				} else {
					if (isset($options['preValue'])) {
						$output .= $options['preValue'];
					}

					$output .= $value;
					if (isset($options['postValue'])) {
						$output .= $options['postValue'];
					}
				}
				$output .= '</dd>';
			}
		}

		return $output;
	}

}
?>
