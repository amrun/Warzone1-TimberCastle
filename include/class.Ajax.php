<?php

/**
 * Diese Klasse wird benötigt um AJAX Requests abzusetzen.
 */
class Ajax {
	
	/**
	 * Diese Methode erstellt eine neue Instanz des Typs "Ajax".
	 */
	public function __construct() {
	}
	
	/**
	 * Diese Methode liest die Dropzones mittels der angegebenen Spielfigur Id aus und gibt sie im JSON Format zurück
	 */
	function getPawnDropzones() {
		$warzone1 = Warzone1::getInstance();
		$stringJSON = $warzone1->move->getPawnDropzones( $_GET['pawnId'] );
		
		print $stringJSON;
		
		exit();
	}
	
	/**
	 * Diese Methode prüft ob die Bewegung der Figur gültig ist.
	 */
	function validateMove() {
		
		return false;
	}
	
	/**
	 * Diese Methode holt sich den Spielzustand und gibt diesen zurück.
	 */
	function getGameState() {
		$warzone1 = Warzone1::getInstance();
		
		if( $_GET['playerHash'] ) {
			print $warzone1->game->getGameState( $_GET['playerHash'] );
		} else {
			print 'NO HASH GIVEN';
		}
		
		exit();
	}
	
	/**
	 * Diese Methode nimmt den Spielzug einer Spielfigur entgegen.
	 */
	function receiveMove() {
		$warzone1 = Warzone1::getInstance();
		$warzone1->move->execute( $_GET['move'] );
		
		return true;
	}

}
?>