<?php

/**
 * Diese Klasse wird benötigt Kriegshandlungen auszutragen.
 */

class War {
	
	/**
	 * Diese Methode erstellt eine neue Instanz des Typs "War".
	 */
	public function __construct() {
	}
	
	/**
	 * TODO
	 */
	static function checkDistance( $xPos1, $yPos1, $xPos2, $yPos2, $range ) {
		$uphp = uphp::getInstance();
		
		$deltaX = $xPos2 - $xPos1;

		$deltaY = $yPos2 - $yPos1;

		if( $deltaX < 0 ) {
			$deltaX = abs( $deltaX );
		}
		if( $deltaY < 0 ) {
			$deltaY = abs( $deltaY );
		}

		$distance = sqrt( pow( $deltaX, 2 ) + pow( $deltaY, 2 ) );

		if( $distance > ( $range + 0.5) ) {
			return false;
		}

		$uphp->log('BRECHT IHM DIE BEINE');
		return true;
	}
	
	/**
	 * Führt die Kampfhandlungen für ein Spiel aus. Zuerst die des Spielers, welcher gerade gezogen hat,
	 * dann für die anderen.
	 *
	 * Eine Figur attackiert jeweils nur diejenige, die ihr am nächsten steht.
	 * (Sofern jene sich in Angriffsreichweite befindet)
	 */
	function execute( $game ) {
		$uphp = uphp::getInstance();
		$players = Player::getByGameId( $game->getId() );
			
		foreach( $players as $key => $tmpPlayer ) {
			// Kampfhandlungen durchführen, wenn es der Spieler ist, welcher eben gezogen hat
			if( $tmpPlayer->getId() == $game->getTurn() ) {
				self::battleForPlayer( $tmpPlayer );
			}
		}

		// Kampfhandlungen für die restlichen Spieler auf dem Feld ausführen
		foreach( $players as $key => $tmpPlayer ) {
			if( $tmpPlayer->getId() != $game->getTurn() ) {
				self::battleForPlayer( $tmpPlayer );
			}
		}

		return true;
	}

	static function battleForPlayer( $player ) {
		$uphp = uphp::getInstance();

		$playerPawns = Pawn::getByPlayerId( $player->getId() );
		$opponentPawns = Pawn::getOpponentPawns( $player->getPlayercount(), $player->getGameId() );

		$uphp->log(count($opponentPawns));
		$uphp->log(count($playerPawns));
		foreach( $playerPawns as $key => $value ) {

			foreach( $opponentPawns as $iKey => $iValue ) {

				if( self::checkDistance( $value->getXPos(), $value->getYPos(), $iValue->getXPos(), $iValue->getYPos(), $value->getAttackRange() ) ) {
					$bashDamage = $value->getBashAttack() - $iValue->getBashArmor();
					$pierceDamage = $value->getPierceAttack() - $iValue->getPierceArmor();
		
					if( $bashDamage < 0 ) {
						$bashDamage = 0;
					}
		
					if( $pierceDamage < 0 ) {
						$pierceDamage = 0;
					}
		
					$iValue->damage( $bashDamage + $pierceDamage );
					$iValue->persist();
				}
			}
		}
	}
}

?>
