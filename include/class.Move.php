<?php

/**
 * Diese Klasse stellt Funktionen zur Verfügung, um Bewegungen von Spielfiguren zu steuern und validieren.
 *
 * @param move Bewegungsinformationen
 */
class Move {
	
	/**
	 * Diese Methode erstellt eine neue Instanz des Typs "Move"
	 */
	public function __construct() {
	}
	
	/**
	 * Diese Methode führt eine Bewegung einer Spielfigur durch.
	 *
	 * @param move Bewegungsinformationen im JSON Format
	 */
	function execute( $move ) {
		$uphp = uphp::getInstance();
		$moveArray = json_decode( str_replace( '\\', '', $move ) );
		
		// Bewegung validieren
		if( $this->validate( $moveArray ) ) {
			foreach( $moveArray as $key => $value ) {
				$x = substr( $value[1], 1, strpos( $value[1], 'y' ) );
				$y = substr( $value[1], strpos( $value[1], 'y' ) + 1 );
				
				$tmpPawn = Pawn::getById( $value[0] );
				$gameId = $tmpPawn->getGameId();
				$playerId = $tmpPawn->getPlayerId();
				
				if( self::checkRangeValidity( $tmpPawn, $x, $y ) ) {
					$tmpPawn->setXPos( $x );
					$tmpPawn->setYPos( $y );
					$tmpPawn->persist();
				}
			}
			
			$tmpPlayer = Player::getById( $playerId );
			$tmpGame = Game::getById( $gameId );
			$opponents = $tmpPlayer->getOpponents( $tmpPlayer->getPlayercount(), $gameId );
			
			$nextPlayer = null;
			$nextPlayerCount = $tmpPlayer->getPlayercount() + 1;
			
			$uphp->log( count( $opponents ) );
			
			foreach( $opponents as $oKey => $oValue ) {
				if( $nextPlayerCount == $oValue->getPlayercount() ) {
					$nextPlayer = $oValue;
				}
			}
			
			if( $nextPlayer != null ) {
				$tmpGame->setTurn( $nextPlayer->getId() );
			} else {
				$tmpGame->setTurn( $opponents[0]->getId() );
			}
			
			$tmpGame->persist();
		
		}
		
		return true;
	}
	
	/**
	 * Diese Methode liest die Dropzones der Spielfigur aus und gibt sie im JSON Format zurück.
	 *
	 * @param pawnId Id der Spielfigur
	 */
	function getPawnDropzones( $pawnId ) {
		$firstTile = array();
		$lastTile = array();
		$dropZones = array();
		
		$pawn = Pawn::getById( $pawnId );
		$player = Player::getById( $pawn->getPlayerId() );
		$game = Game::getById( $player->getGameId() );
		
		// holt die maximalen Koordinatenwerte zur Validierung der Positionen
		$maxXY = $this->getMaxXY( $game->getGameSet( $game->getSetName() ) );
		
		$firstTile['y'] = $pawn->getYPos() - $pawn->getWalkRange();
		$firstTile['x'] = $pawn->getXPos() - $pawn->getWalkRange();
		
		$lastTile['y'] = $pawn->getYPos() + $pawn->getWalkRange();
		$lastTile['x'] = $pawn->getXPos() + $pawn->getWalkRange();
		
		$i = $firstTile['y'];
		$tileCount = 0;
		
		while( $i <= $lastTile['y'] ) {
			$j = $firstTile['x'];
			
			while( $j <= $lastTile['x'] ) {
				if( $this->isValidField( $j, $i, $maxXY, $pawn ) ) {
					$dropZones[$tileCount] = 'x' . $j;
					$dropZones[$tileCount] .= 'y' . $i;
					$tileCount++;
				}
				
				$j++;
			}
			
			$i++;
		}
		
		$dropzonesJson = json_encode( $dropZones );
		
		return $dropzonesJson;
	}
	
	/**
	 * Diese Methode gibt die Koordinaten des Feldes (unten rechts) als array zurück.
	 *
	 * @param gameSet GameSet
	 */
	function getMaxXY( $gameSet ) {
		$maxValues = array();
		
		$maxValues['x'] = $gameSet->map->xCount;
		$maxValues['y'] = $gameSet->map->yCount;
		
		return $maxValues;
	}
	
	/**
	 * Diese Methode prüft ob die angegebene an auf das Feld (mittels angegebener Koordinaten) bewegt werden kann. Falls ja, gibt es true zurück ansonsten false.
	 *
	 * @param x X Zielposition
	 * @param y Y Zielposition
	 * @param maxXY Maximale X und Y Koordinaten des Spielfelds
	 * @param pawnId Id der Spielfigur
	 */
	function isValidField( $x, $y, $maxXY, $pawn ) {
		$uphp = uphp::getInstance();
		
		// Überprüfung ob die Koordinaten innerhalb der Map sind
		if( $x <= 0 || $y <= 0 || $x > $maxXY['x'] || $y > $maxXY['y'] ) {
			return false;
		}
		
		// Überprüfung ob ein anderer pawn auf dem feld steht
		$count = $uphp->comm->mysqlCount( 'pawn', 'id', 'xpos = \'' . $x . '\' AND ypos = \'' . $y . '\'' );
		
		if( $count > 0 ) {
			return false;
		}
		
		// Überprüfung ob das Feld in reichweite liegt
		return self::checkRangeValidity( $pawn, $x, $y );
	}
	
	
	/**
	 * Diese Methode prüft ob die Spielfigur auf die angegebene X und Y Koordinate bewegt werden kann. Je nach Spielfigurtyp kann die Reichweite varieren.
	 *
	 * @param pawn Die zu bewegende Spielfigur
	 * @param xTarget X Zielposition
	 * @param yTarget Y Zielposition
	 */
	static function checkRangeValidity( $pawn, $xTarget, $yTarget ) {
		$deltaX = $xTarget - $pawn->getXPos();
		
		if( $deltaX < 0 ) {
			$deltaX = abs( $deltaX );
		}
		
		$deltaY = $yTarget - $pawn->getYPos();
		
		if( $deltaY < 0 ) {
			$deltaY = abs( $deltaY );
		}
		
		$distance = sqrt( pow( $deltaX, 2 ) + pow( $deltaY, 2 ) );
		
		if( $distance > ( $pawn->getWalkRange() + 0.5) ) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * Diese Methode validiert sämtliche Bewegungen
	 *
	 * @param moveArray Bewegungen
	 */
	function validate( $moveArray ) {
		//TODO: validate the move
		return true;
	}
}

?>