<?php


/**
 * Diese Klasse verwaltet die Spiele
 */
class Game {
	
	public $id = 0;
	public $status = null;
	public $turn = null;
	public $setName = null;
	
	/**
	 * Diese Methode erstellt eine Instanz vom Typ "Game"
	 */
	public function __construct() {
	}
	
	/**
	 * Diese Methode beendet das Spiel, welches unter der angegebenen Id läuft
	 *
	 * @param id Id des Spiels
	 */
	function endGame( $id ) {
		
		return false;
	}
	
	/**
	 * Diese Methode rendert das Spiel fuer den Spieler (welcher durch den Hash erkannt wird)
	 *
	 * @param playerHash Hash des Spielers
	 */
	public function renderBaseGame( $playerHash ) {
		$warzone1 = Warzone1::getInstance();
		$uphp = uphp::getInstance();
		$player = Player::getByHash( $playerHash );
		
		$game = self::getById( $player->getGameId() );
		$gameSet = $game->getGameSet( $game->getSetName() );
		$map = $warzone1->map->generate( $gameSet->map->xCount, $gameSet->map->yCount );
		
		$js = file_get_contents( './js/game.js' );
		$js = str_replace( '###PLAYERHASH###', $playerHash, $js );
		$js = str_replace( '###BASEURL###', $uphp->conf->path->baseUrl, $js );
		$uphp->assembler->additionalJS( $js );
		
		return $map;
	}
	
	/**
	 * Diese Methode startet ein Spiel wenn beide Email-Adressen angegeben sind.
	 *
	 * @param emailP1 Email-Adresse des ersten Spielers
	 * @param emailP2 Email-Adresse des zweiten Spielers
	 */
	function startGame( $emailP1, $emailP2, $gameSet = 'set1' ) {
		$i = 0;
		$playerArray = array();
		
		$gameSet = self::getGameSet( $gameSet );
		
		// Create New Game
		$game = new Game();
		$game->setSetName( $gameSet['name'] );
		$game->persist();
		
		// Create Player1
		$playerArray[$i] = new Player();
		$playerArray[$i]->setGameId( $game->getId() );
		$playerArray[$i]->setHash( Player::generateHash( $emailP1 ) );
		$playerArray[$i]->setEmail( $emailP1 );
		$playerArray[$i]->setPlayercount( $i );
		$playerArray[$i]->persist();
		$i++;
		
		// Create Player2
		$playerArray[$i] = new Player();
		$playerArray[$i]->setGameId( $game->getId() );
		$playerArray[$i]->setHash( Player::generateHash( $emailP2 ) );
		$playerArray[$i]->setEmail( $emailP2 );
		$playerArray[$i]->setPlayercount( $i );
		$playerArray[$i]->persist();
		$i++;
		
		// Player 1 als "am Zug" setzen
		$game->setTurn( $playerArray[0]->getId() );
		$game->persist();
		
		// loop durch playerarray
		foreach( $playerArray as $key => $value ) {
			//loop durch in gameset definierte player
			foreach( $gameSet->players->player as $tmpPlayer ) {
				if( $tmpPlayer['playercount'] == $value->getPlayercount() ) {
					//pawns für player erstellen
					foreach( $tmpPlayer->pawns->pawn as $tmpPawn ) {
						$pawn = Pawn::getPredefinedPawn( $tmpPawn['name'] );
						
						if( $pawn != null ) {
							$pawn->setXPos( (string) $tmpPawn->xPos );
							$pawn->setYPos( (string) $tmpPawn->yPos );
							$pawn->setPlayerId( $value->getId() );
							$pawn->setGameId( $game->getId() );
							$pawn->persist();
						} else {
							echo 'pawn ' . $tmpPawn['name'] . ' was not found <br>';
						}
					}
				}
			}
		}
		
		return $game->getId();
	}
	
	/**
	 * Diese Methode gibt den Status des Spiels zurück. Das Spiel wird mittels dem Hash des Spielers ermittelt.
	 *
	 * @param playerHash Hash des Spielers
	 */
	function getGameState( $playerHash ) {
		$gameStateJSON = '';

		// get active player and its pawns
		$activePlayer = Player::getByHash( $playerHash );
		$activePlayerPawns = Pawn::getByPlayerId( $activePlayer->getId() );
		
		// get pawns of all opponents
		$passivePlayerPawns = Pawn::getOpponentPawns( $activePlayer->getPlayerCount(), $activePlayer->getGameId() );
		
		$gameStateArray[] = $activePlayerPawns;
		$gameStateArray[] = $passivePlayerPawns;
		$gameStateJSON = json_encode( $gameStateArray );
		
		return $gameStateJSON;
	}
	
	function getGameSet( $setName ) {
		// read gameSets.xml
		$gameSets = simplexml_load_file( './config/gameSets.xml' );
		
		foreach( $gameSets->set as $tmpSet ) {
			if( $tmpSet['name'] == $setName ) {
				$gameSet = $tmpSet;
				break;
			}
		}
		
		return $gameSet;
	}
	
	/**
	 * Diese Methode schreibt die Spielinformationen in die Datenbank.
	 */
	function persist() {
		$uphp = uphp::getInstance();
		$query = '';
		
		$needsUpdate = $uphp->comm->mysqlCount( 'game', 'id', 'id = \'' . $this->id . '\'' );
		
		if( $needsUpdate > 0 ) {
			$query = 'UPDATE game SET status = \'' . $this->status . '\', turn = \'' . $this->turn . '\', setname = \'' . $this->setName . '\' WHERE id = \'' . $this->id . '\'';
		} else {
			$query = 'INSERT INTO game ( status, turn, setname ) ';
			$query .= 'VALUES ( \'' . $this->status . '\', \'' . $this->turn . '\', \'' . $this->setName . '\' )';
		}
		
		$res = $uphp->comm->mysqlQuery( $query );
		
		if( ! $needsUpdate ) {
			$this->id = mysql_insert_id();
		}
	}
	
	/**
	 * Diese Methode gibt ein Spiel-Objekt zurück anhand der angegebenen Id des gesuchten Spiels.
	 *
	 * @param id Id des Spiels
	 */
	function getById( $id ) {
		$uphp = uphp::getInstance();
		$game = null;
		
		$result = $uphp->comm->mysqlSelect( 'game', '*', 'id = ' . $id );
		
		if( count( $result ) > 0 ) {
			$game = self::setObjectValues( $result[0] );
		}
		
		return $game;
	}
	
	/**
	 * Diese Methode weist die Datenbankinformationen an einer Spiel-Instanz zu und gibt das neue Objekt zurück.
	 *
	 * @param result Datenbankinformation
	 */
	private static function setObjectValues( $result ) {
		$obj = new Game();
		
		$obj->id = $result['id'];
		$obj->status = $result['status'];
		$obj->turn = $result['turn'];
		$obj->setName = $result['setname'];
		
		return $obj;
	}
	
	/**
	 * Diese Methode gibt die Id des Spiels.
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Diese Methode setzt die Id des Spiels.
	 *
	 * @param id Id des Spiels
	 */
	public function setId( $id ) {
		$this->id = $id;
	}
	
	/**
	 * Diese Methode gibt den Status des Spiels zurück.
	 */
	public function getStatus() {
		return $this->status;
	}
	
	/**
	 * Diese Methode setzt den Status des Spiels.
	 *
	 * @param status Status des Spiels
	 */
	public function setStatus( $status ) {
		$this->status = $status;
	}
	
	/**
	 * Diese Methode gibt den Turn des Spiels zurück.
	 */
	public function getTurn() {
		return $this->turn;
	}
	
	/**
	 * Diese Methode setzt den Turn des Spiels.
	 *
	 * @param turn Turn des Spiels
	 */
	public function setTurn( $turn ) {
		$this->turn = $turn;
	}
	
	/**
	 * Diese Methode gibt den SetName des Spiels zurück. 
	 */
	public function getSetName() {
		return $this->setName;
	}
	
	/**
	 * Diese Methode setzt den SetName des Spiels.
	 *
	 * @param setName SetName des Spiels
	 */
	public function setSetName( $setName ) {
		$this->setName = $setName;
	}
}

?>
