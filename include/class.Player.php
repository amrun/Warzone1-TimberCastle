<?php

/**
 * Diese Klasse verwaltet die Spieler
 */
class Player {
	
	public $id = 0;
	public $gameId = 0;
	public $hash = null;
	public $email = null;
	public $playercount = null;
	
	/**
	 * Diese Methode erstellt eine Instanz vom Typ "Player"
	 */
	public function __construct() {
	}
	
	/**
	 * Diese Methode schreibt die Spielerinformationen in die Datenbank.
	 */
	function persist() {
		$uphp = uphp::getInstance();
		$query = '';
		
		$needsUpdate = $uphp->comm->mysqlCount( 'player', 'id', 'id = \'' . $this->id . '\'' );
		
		if( $needsUpdate > 0 ) {
			$query = 'UPDATE player SET game_id = \'' . $this->gameId . '\', hash = \'' . $this->hash . '\', playercount = \'' . $this->playercount . '\', email = \'' . $this->email . '\' WHERE id = ' . $this->id;
		} else {
			$query = 'INSERT INTO player ( game_id, hash, playercount, email ) ';
			$query .= 'VALUES( \'' . $this->gameId . '\', \'' . $this->hash . '\', \'' . $this->playercount . '\', \'' . $this->email . '\' )';
		}
		
		$res = $uphp->comm->mysqlQuery( $query );
		
		if( !$needsUpdate ) {
			$this->id = mysql_insert_id();
		}
	}
	
	/**
	 * Diese Methode gibt ein Spieler-Objekt zurück anhand der angegebenen Id des gesuchten Spielers.
	 *
	 * @param id Id des Spielers
	 */
	static function getById( $id ) {
		$uphp = uphp::getInstance();
		$player = null;
		
		$result = $uphp->comm->mysqlSelect( 'player', '*', 'id = \'' . $id . '\'' );
		
		if( count( $result ) > 0 ) {
			$player = self::setObjectValues( $result[0] );
		}
		
		return $player;
	}
	
	/**
	 * Diese Methode gibt Spieler-Objekte zurück anhand der angegebenen SpielId.
	 *
	 * @param gameId Id des Spiels
	 */
	static function getByGameId( $gameId ) {
		$uphp = uphp::getInstance();
		$ret = array();
		$result = $uphp->comm->mysqlSelect( 'player', '*', 'game_id = \'' . $gameId . '\'' );
		
		foreach( $result as $player ) {
			$ret[] = self::setObjectValues( $player );
		}
		
		return $ret;
	}
	
	/**
	 * Diese Methode gibt ein Spieler-Objekt zurück anhand des angegebenen Hash des gesuchten Spielers.
	 *
	 * @param hash Hash-Wert des Spielers
	 */
	static function getByHash( $hash ) {
		$uphp = uphp::getInstance();
		$player = null;
		$result = $uphp->comm->mysqlSelect( 'player', '*', 'hash = \'' . $hash . '\'' );
		
		if( count( $result ) > 0 ) {
			$player = self::setObjectValues( $result[0] );
		}
		
		return $player;
	}
	
	/**
	 * Diese Methode gibt alle Spieler-Objekte zurück, welche dieselbe Spiel Id besitzen
	 * 
	 * @param $gameId Spiel Id
	 */
	static function getOpponents( $activePlayerCount, $gameId ) {
		$uphp = uphp::getInstance();
		$players = array();
		$objs = $uphp->comm->mysqlSelect( 'player', '*', 'game_id = \'' . $gameId . '\' AND playercount <> \'' . $activePlayerCount . '\'' );

		foreach( $objs as $key => $value ) {
			$players[] = self::setObjectValues( $value );
		}
		
		return $players;
	}
	
	/**
	 * Diese Methode generiert mittels der angegebenen Email-Adresse einen Hash.
	 * 
	 * @param $email Email-Adresse des Spielers
	 */
	static function generateHash( $email ) {
		return md5( $email . time() );
	}
	
	/**
	 * Diese Methode weist die Datenbankinformationen an einer Player-Instanz zu und gibt das neue Objekt zurück.
	 *
	 * @param result Datenbankinformation
	 */
	private static function setObjectValues( $result ) {
		$obj = new Player();
		
		$obj->id = $result['id'];
		$obj->gameId = $result['game_id'];
		$obj->hash = $result['hash'];
		$obj->playercount = $result['playercount'];
		$obj->email = $result['email'];
		
		return $obj;
	}
	
	/**
	 * Diese Methode gibt die Id des Spielers zurück.
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Diese Methode setzt die Id des Spielers.
	 *
	 * @param id Id des Spielers
	 */
	public function setId( $id ) {
		$this->id = $id;
	}
	
	/**
	 * Diese Methode gibt die Spiel-Id (gameId) des Spielers zurück.
	 */
	public function getGameId() {
		return $this->gameId;
	}
	
	/**
	 * Diese Methode setzt die Spiel-Id (gameId) des Spielers.
	 *
	 * @param id Spiel-Id
	 */
	public function setGameId( $gameId ) {
		$this->gameId = $gameId;
	}
	
	/**
	 * Diese Methode gibt den Hash des Spielers zurück.
	 */
	public function getHash() {
		return $this->hash;
	}
	
	/**
	 * Diese Methode setzt die Id des Spielers.
	 *
	 * @param hash Hash des Spielers
	 */
	public function setHash( $hash ) {
		$this->hash = $hash;
	}
	
	/**
	 * Diese Methode gibt die Spielernummer des Spielers zurück.
	 */
	public function getPlayercount() {
		return $this->playercount;
	}
	
	/**
	 * Diese Methode setzt die Spielernummer des Spielers.
	 *
	 * @param playercount Spielernummer des Spielers
	 */
	public function setPlayercount( $playercount ) {
		$this->playercount = $playercount;
	}
	
	/**
	 * Diese Methode gibt die Email-Adresse des Spielers zurück.
	 */
	public function getEmail() {
		return $this->email;
	}
	
	/**
	 * Diese Methode setzt die Email-Adresse des Spielers.
	 *
	 * @param email Email-Adresse des Spielers
	 */
	public function setEmail( $email ) {
		$this->email = $email;
	}
}

?>