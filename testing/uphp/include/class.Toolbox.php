<?php
/**
 * Toolbox Class. Provides miscellaneous functions
 * @author amrun
 *
 */
class Toolbox {
	public function objectToArray( $object ) {
		
		
		foreach( $object as $key => $value ) {
			$result[(string) $key] = (string) $value;
		}
		
		return $result;
	}
}

?>