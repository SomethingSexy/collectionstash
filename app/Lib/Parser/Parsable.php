<?php
interface Parsable {
	/**
	 * This will return a format that we know how to save.  That way each manufacturer will have to 
	 * work with if this returns a valid response then we will create the collectible and attempt
	 * to prefill as much data as we can, including images.  We should also serialize this data to
	 * json and save it with the collectible so that we have reference to what was used to parse for debugging
	 */
	public function parse($contents);
}
?>