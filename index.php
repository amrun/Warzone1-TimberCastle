<?php
/*
ini_set('display_errors', 1);
ini_set('log_errors', 1);
//ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
error_reporting(E_ALL);
*/

// autoload instanciated classes
function __autoload( $className ) {
	if( $className == 'uphp' ) {
		require_once 'uphp/class.' . $className . '.php';
		
		return true;
	} elseif( $className == 'Warzone1' ) {
		require_once 'class.' . $className . '.php';
		
		return true;
	}
	
	if( file_exists( 'include/class.' . $className . '.php' ) ) {
		require_once 'include/class.' . $className . '.php';
		
		return true;
	}
	
	return false;
}

// Initiert die nötigen Spielkomponenten
$warzone = Warzone1::getInstance();
$warzone->init();

?>
