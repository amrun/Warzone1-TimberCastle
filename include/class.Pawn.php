<?php

/**
 * Diese Klasse verwaltet die Spielfiguren
 */
class Pawn {
	
	public $id = 0;
	public $playerId = null;
	public $gameId = null;
	public $name = null;
	public $attackRange = null;
	public $bashArmor = null;
	public $bashAttack = null;
	public $pierceArmor = null;
	public $pierceAttack = null;
	public $hitpoints = null;
	public $walkRange = null;
	public $xPos = null;
	public $yPos = null;
	
	/**
	 * Diese Methode erstellt eine Instanz vom Typ "Pawn"
	 */
	public function __construct() {
	}
	
	/**
	 * Diese Methode erstellt eine Instanz vom Typ "Pawn"
	 *
	 * @param amount Wert des Schadens
	 * @param armorType Angriffstyp
	 */
	function damage( $amount, $armorType ) {
		return false;
	}
	
	/**
	 * Diese Methode schreibt die Spielfigurinformationen in die Datenbank.
	 */
	function persist() {
		$uphp = uphp::getInstance();
		$query = '';
		
		$needsUpdate = $uphp->comm->mysqlCount( 'pawn', 'id', 'id = \'' . $this->id . '\'' );
		
		if( $needsUpdate > 0 ) {
			$query = 'UPDATE pawn SET name = \'' . $this->name . '\', game_id = \'' . $this->gameId . '\', player_id = \'' . $this->playerId . '\', hitpoints = \'' . $this->hitpoints . '\', pierceAttack = \'' . $this->pierceAttack . '\', pierceattack = \'' . $this->pierceAttack . '\', piercearmor = \'' . $this->pierceArmor . '\', bashattack = \'' . $this->bashAttack . '\', basharmor = \'' . $this->bashArmor . '\', attackrange = \'' . $this->attackrange . '\', walkrange = \'' . $this->walkRange . '\', xpos = \'' . $this->xPos . '\', ypos = \'' . $this->yPos . '\' WHERE id = \'' . $this->id . '\'';
		} else {
			$query = 'INSERT INTO pawn ( name, game_id, player_id, hitpoints, pierceattack, piercearmor, bashattack, basharmor, attackrange, walkrange, xpos, ypos ) ';
			$query .= 'VALUES ( \'' . $this->name . '\', \'' . $this->gameId . '\', \'' . $this->playerId . '\', \'' . $this->hitpoints . '\', \'' . $this->pierceAttack . '\', \'' . $this->pierceArmor . '\', \'' . $this->bashAttack . '\', \'' . $this->bashArmor . '\', \'' . $this->attackRange . '\', \'' . $this->walkRange . '\', \'' . $this->xPos . '\', \'' . $this->yPos . '\' )';
		}
		
		$res = $uphp->comm->mysqlQuery( $query );
		
		if( ! $needsUpdate ) {
			$this->id = mysql_insert_id();
		}
	}
	
	/**
	 * Diese Methode gibt ein Spielfigur-Objekt zurück anhand der angegebenen Id der gesuchten Spielfigur.
	 *
	 * @param id Id der Spielfigur
	 */
	static function getById( $id ) {
		$uphp = uphp::getInstance();
		$pawn = null;
		$result = $uphp->comm->mysqlSelect( 'pawn', '*', 'id = ' . $id );
		
		if( count( $result ) > 0 ) {
			$pawn = self::setObjectValues( $result[0] );
		}
		
		return $pawn;
	}
	
	/**
	 * Diese Methode gibt anhand der angegebenen Id des Spielers Spielfigur-Objekte zurück.
	 *
	 * @param playerId Id des Spielers
	 */
	static function getByPlayerId( $playerId ) {
		$uphp = uphp::getInstance();
		$pawns = array();
		
		$objs = $uphp->comm->mysqlSelect( 'pawn', '*', 'player_id = \'' . $playerId . '\'' );
		
		foreach( $objs as $key => $value ) {
			$pawns[] = self::setObjectValues( $value );
		}
		
		return $pawns;
	}
	
	/**
	 * Diese Methode holt alle Gegnerspieler eines Spielers
	 * 
	 * @param int $activePlayerCount
	 * @param int $gameId
	 */
	static function getOpponentPawns( $activePlayerCount, $gameId ) {
		$uphp = uphp::getInstance();
		$tempOpIds = array();
		$pawns = array();
		
		$opponents = Player::getOpponents( $activePlayerCount, $gameId );
		
		foreach( $opponents as $key => $value ) {
			$tempOpIds[] = $value->getId();
		}
		
		$opIds = implode( ',', $tempOpIds );
		$objs = $uphp->comm->mysqlSelect( 'pawn', '*', 'player_id IN ( ' . $opIds . ' )' );
		
		foreach( $objs as $key => $value ) {
			$pawns[] = self::setObjectValues( $value );
		}
		
		return $pawns;
	
	}
	
	/**
	 * Diese Methode weist die Datenbankinformationen an einer Spielfigur-Instanz zu und gibt das neue Objekt zurück.
	 *
	 * @param result Datenbankinformation
	 */
	private static function setObjectValues( $result ) {
		$obj = new Pawn();
		
		$obj->id = $result['id'];
		$obj->gameId = $result['game_id'];
		$obj->playerId = $result['player_id'];
		$obj->name = $result['name'];
		$obj->attackRange = $result['attackrange'];
		$obj->bashAttack = $result['bashattack'];
		$obj->bashArmor = $result['basharmor'];
		$obj->pierceAttack = $result['pierceattack'];
		$obj->pierceArmor = $result['piercearmor'];
		$obj->hitpoints = $result['hitpoints'];
		$obj->walkRange = $result['walkrange'];
		$obj->xPos = $result['xpos'];
		$obj->yPos = $result['ypos'];
		
		return $obj;
	}
	
	
	/**
	 * Diese Methode liest aus der Datei config/pawns.xml eine vordefinierte Spielfigur aus
	 * @param string $name
	 */
	public static function getPredefinedPawn( $name ) {
		$ret = null;
		$uphp = uphp::getInstance();
		$pawns = simplexml_load_file( 'config/pawns.xml' );
		
		foreach( $pawns->pawns->pawn as $pawn ) {
			$tmpName = (string) $pawn->name;
			
			if( $tmpName == (string) $name ) {
				$ret = new Pawn();
				$ret->name = $tmpName;
				$ret->attackRange = (int) $pawn->attackRange;
				$ret->bashAttack = (int) $pawn->bashAttack;
				$ret->bashArmor = (int) $pawn->bashArmor;
				$ret->pierceAttack = (int) $pawn->pierceAttack;
				$ret->pierceArmor = (int) $pawn->pierceArmor;
				$ret->hitpoints = (int) $pawn->hitpoints;
				$ret->walkRange = (int) $pawn->walkRange;
				break;
			}
		}
		
		return $ret;
	}
	
	// getters and setters
	

	/**
	 * Diese Methode gibt die Id der Spielfigur zurück.
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Diese Methode setzt die Id der Spielfigur.
	 *
	 * @param id Id der Spielfigur
	 */
	public function setId( $id ) {
		$this->id = $id;
	}

	/**
	 * Diese Methode gibt die Spiel-Id der Spielfigur zurück.
	 */
	public function getGameId() {
		return $this->gameId;
	}
	
	/**
	 * Diese Methode setzt die Spiel-Id der Spielfigur.
	 *
	 * @param id Id der Spielfigur
	 */
	public function setGameId( $gameId ) {
		$this->gameId = $gameId;
	}
	
	/**
	 * Diese Methode gibt die Id des referenzierten Spieler der Spielfigur zurück.
	 */
	public function getPlayerId() {
		return $this->playerId;
	}
	
	/**
	 * Diese Methode setzt referenzierten Spieler der Spielfigur.
	 *
	 * @param playerId referenzierten Spieler der Spielfigur
	 */
	public function setPlayerId( $playerId ) {
		$this->playerId = $playerId;
	}
	
	/**
	 * Diese Methode gibt den Namen der Spielfigur zurück.
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Diese Methode setzt den Namen der Spielfigur.
	 *
	 * @param name Name der Spielfigur
	 */
	public function setName( $name ) {
		$this->name = $name;
	}
	
	/**
	 * Diese Methode gibt die Angriffsreichweite der Spielfigur zurück.
	 */
	public function getAttackRange() {
		return $this->attackRange;
	}
	
	/**
	 * Diese Methode setzt die Angriffsreichweite der Spielfigur.
	 *
	 * @param attackRange Angriffsreichweite der Spielfigur
	 */
	public function setAttackRange( $attackRange ) {
		$this->attackRange = $attackRange;
	}
	
	/**
	 * Diese Methode gibt den Schlagverteidigungswert der Spielfigur zurück.
	 */
	public function getBashArmor() {
		return $this->bashArmor;
	}
	
	/**
	 * Diese Methode setzt den Schlagverteidigungswert der Spielfigur.
	 *
	 * @param bashArmor Schlagverteidigungswertv der Spielfigur
	 */
	public function setBashArmor( $bashArmor ) {
		$this->bashArmor = $bashArmor;
	}
	
	/**
	 * Diese Methode gibt den Schlagangriffswert der Spielfigur zurück.
	 */
	public function getBashAttack() {
		return $this->bashAttack;
	}
	
	/**
	 * Diese Methode setzt den Schlagangriffswert der Spielfigur.
	 *
	 * @param bashAttack Schlagangriffswert der Spielfigur
	 */
	public function setBashAttack( $bashAttack ) {
		$this->bashAttack = $bashAttack;
	}
	
	/**
	 * Diese Methode gibt den Stichverteidigungswert der Spielfigur zurück.
	 */
	public function getPierceArmor() {
		return $this->pierceArmor;
	}
	
	/**
	 * Diese Methode setzt den Stichverteidigungswert der Spielfigur.
	 *
	 * @param pierceArmor Stichverteidigungswert der Spielfigur
	 */
	public function setPierceArmor( $pierceArmor ) {
		$this->pierceArmor = $pierceArmor;
	}
	
	/**
	 * Diese Methode gibt den Stichangriffswert der Spielfigur zurück.
	 */
	public function getPierceAttack() {
		return $this->pierceAttack;
	}
	
	/**
	 * Diese Methode setzt den Stichangriffswert der Spielfigur.
	 *
	 * @param pierceAttack Stichangriffswert der Spielfigur
	 */
	public function setPierceAttack( $pierceAttack ) {
		$this->pierceAttack = $pierceAttack;
	}
	
	/**
	 * Diese Methode gibt die Lebenspunkte der Spielfigur zurück.
	 */
	public function getHitpoints() {
		return $this->hitpoints;
	}
	
	/**
	 * Diese Methode setzt die Lebenspunkte der Spielfigur.
	 *
	 * @param hitpoints Lebenspunkte der Spielfigur
	 */
	public function setHitpoints( $hitpoints ) {
		$this->hitpoints = $hitpoints;
	}
	
	/**
	 * Diese Methode gibt den ??? der Spielfigur zurück.
	 */
	public function getWalkrange() {
		return $this->walkRange;
	}
	
	/**
	 * Diese Methode setzt den Bewegungsreichweitenwert der Spielfigur.
	 *
	 * @param walkRange Bewegungsreichweitenwert der Spielfigur
	 */
	public function setWalkrange( $walkRange ) {
		$this->walkRange = $walkRange;
	}
	
	/**
	 * Diese Methode gibt die X-Position der Spielfigur zurück.
	 */
	public function getXPos() {
		return $this->xPos;
	}
	
	/**
	 * Diese Methode setzt die X-Position der Spielfigur.
	 *
	 * @param xPos X-Position der Spielfigur
	 */
	public function setXPos( $xPos ) {
		$this->xPos = $xPos;
	}
	
	/**
	 * Diese Methode gibt die Y-Position der Spielfigur zurück.
	 */
	public function getYPos() {
		return $this->yPos;
	}
	
	/**
	 * Diese Methode setzt die Y-Position der Spielfigur.
	 *
	 * @param xPos Y-Position der Spielfigur
	 */
	public function setYPos( $yPos ) {
		$this->yPos = $yPos;
	}
}

?>
