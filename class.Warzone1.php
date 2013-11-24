<?php

/**
 * Diese Klasse ist das Herzstück des Spiels.
 * Hier werden die HTTP Requests abgefangen und an die entsprechenden Module weitergeleitet.
 */
class Warzone1 {
	
	protected static $_instance;
	public $uphp = null;
	public $move = null;
	public $map = null;
	public $war = null;
	public $ajax = null;
	public $game = null;
	
	/**
	 * Diese Method erstellt eine Instanz der Warzone1-Klasse.
	 * Ausserdem werden Instanzen der zugehörigen Module erstellt, welche zur Verarbeitung der Spielvorgänge benoetigt werden.
	 *
	 */
	private function __construct() {
		$this->uphp = uphp::getInstance();
		$this->ajax = new Ajax();
		$this->game = new Game();
		$this->map = new Map();
		$this->move = new Move();
		$this->war = new War();
	}
	
	/**
	 * Diese Methode entspricht dem Singleton-Pattern und gibt eine Instanz der Warzone1-Klasse zurück.
	 */
	public static function getInstance() {
		if( self::$_instance === NULL ) {
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * Diese Methode initialisiert den angegebenen (durch Parameter $_GET['stage']) Seitenaufruf.
	 * Dem Parameter koennen folgende Werte zugewiesen werden:
	 * <ul>
	 * <li>welcome -> Willkommensseite</li>
	 * <li>confirm -> Spielstart Bestaetigung</li>
	 * <li>startGame -> Startet ein Spiel</li>
	 * <li>game -> Bereits gestartetes Spiel anzeigen</li>
	 * <li>ajaxRequest -> AJAX Request absetzen</li>
	 * </ul>
	 * Wird dem Parameter ein anderer Wert zugewiesen, so wird die Willkommensseite angezeigt.
	 */
	public function init() {
		$this->uphp->assembler->additionalCSS( file_get_contents( './css/warzone1.css' ) );
		
		switch( $_GET['stage'] ) {
			
			case 'welcome':
				$this->welcome();
				break;
			
			case 'confirm':
				$this->confirm();
				break;
			
			case 'game':
				$this->game();
				break;
			
			case 'ajaxRequest':
				$this->ajaxRequest();
				break;
			
			case 'end':
				$this->end();
				break;
			
			default:
				$this->welcome();
				break;
		}
	}
	
	/**
	 * Diese Methode generiert die Willkommensseite
	 */
	private function welcome() {
		$content = file_get_contents( './stagePages/welcome.txt' );
		$this->uphp->assembleHtml( $content, 'Willkommen', true );
	}
	
	/**
	 * Diese Methode generiert die Bestätigungsseite
	 */
	private function confirm() {
		$gameId = $this->game->startGame( $_POST['txtEmailP1'], $_POST['txtEmailP2'] );
		
		$players = Player::GetByGameId( $gameId );
		
		if( count( $players ) > 0 ) {
			$mail = array();
			$content = file_get_contents( './stagePages/confirm.txt' );
			
			for( $i = 1; $i < count( $players ); $i++ ) {
				$mail['to'] = $players[$i]->getEmail();
				$mail['subject'] = "Warzone1 Spieleinladung";
				$mail['body'] = "Sie wurden von " . $players[0]->getEmail() . " zu einer Warzone1 Spielsession eingeladen.\n" . "Klicken Sie auf folgenden Link, um an diesem Spiel teilzunehmen:\n" . $this->uphp->conf->path->baseUrl . "?stage=game&playerHash=" . $players[$i]->getHash() . "\n\n\n" . "Das Warzone1 Team wuenscht Ihnen viel Vergnuegen.";
				
				//mail( $mail['to'], $mail['subject'], $mail['body'], $mail['options'] );
			}
			
			$content = str_replace( "###PLAYERHASH###", $players[0]->getHash(), $content );
			
			// TEMPORAERE LOESUNG - START
			$content = str_replace( '###MAILTO###', $mail['to'], $content );
			$content = str_replace( '###MAILSUBJECT###', $mail['subject'], $content );
			$content = str_replace( '###MAILBODY###', $mail['body'], $content );
			$content = str_replace( '###MAILOPTIONS###', $mail['options'], $content );
			// TEMPORAERE LOESUNG - END
			

			$this->uphp->assembleHtml( $content, 'Bestätigung', true );
		} else {
			// FAILURE
		}
	}
	
	/**
	 * Diese Methode generiert die Spiel-Seite des aktuellen Spiels
	 */
	private function game() {
		$content = file_get_contents( './stagePages/game.txt' );
		// Kontrolle ob Player existiert
		$tmpPlayer = Player::getByHash( $_GET['playerHash'] );
		$autoreload = null;
		
		if( $tmpPlayer != null ) {
			$tmpGame = Game::getById( $tmpPlayer->getGameId() );
			
			if( $tmpGame != null ) {
				if( $tmpGame->getTurn() == $tmpPlayer->getId() ) {
					$content = str_replace( '###MAP###', $this->game->renderBaseGame( $_GET['playerHash'] ), $content );
				} else {
					$content = str_replace( '###MAP###', '<div class="nyt">Sie sind noch nicht am Zug!<br />Die Seite wird automatisch neu geladen, bis Ihre Gegner fertig sind.</div>', $content );
					$autoreload = 'setTimeout ( "location.reload()", 5000 );';
				}
			} else {
				$content = str_replace( '###MAP###', 'Das Spiel mit der ID ' . $tmpPlayer->getGameId . ' existiert nicht!', $content );
			}
		} else {
			$content = str_replace( '###MAP###', 'Der Player mit dem Hash ' . $_GET['playerHash'] . ' existiert nicht!', $content );
		}
		
		if( $autoreload != null ) {
			$this->uphp->assembler->additionalJS( $autoreload );
		}
		
		$this->uphp->assembleHtml( $content, 'Battle!', true );
	}
	
	/**
	 * Diese Methode generiert die Spielende Seite
	 */
	private function end() {
		$content = file_get_contents( './stagePages/end.txt' );
		$this->uphp->assembleHtml( $content, 'Ende', true );
	}
	
	/**
	 * Diese Methode behandelt einen AJAX Request mittels dem Parameter $_GET['request'].
	 * Folgende Requests werden behandelt:
	 * 
	 * <ul>
	 * <li>getGameState</li>
	 * <li>getPawnDropzones</li>
	 * <li>game</li>
	 * <li>ajax</li>
	 * </ul>
	 * 
	 */
	private function ajaxRequest() {
		switch( $_GET['request'] ) {
			
			case 'getGameState':
				$this->ajax->getGameState();
				break;
			
			case 'getPawnDropzones':
				$this->ajax->getPawnDropzones();
				break;
			
			case 'receiveMove':
				$this->ajax->receiveMove();
				break;
		}
	}
}

?>
