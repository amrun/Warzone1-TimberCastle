<?php

$step = 0;
require_once( '../uphp/class.uphp.php' );
require_once( '../include/class.Player.php' );

////////////////////////////////////////////////////////////////

$step++;
echo $step . ".) Tabelle player wird geleert... ";
$uphp = uphp::getInstance();
if( $uphp->comm->mysqlTruncate( 'player' ) ) {
	echo " OK<br />\n";
}
else {
	echo " NOK<br />\n";
	die();
}

////////////////////////////////////////////////////////////////

$step++;
echo $step . ".) Spieler wird erstellt... ";
$player = new Player();
$player->setGameId( 1 );
$player->setHash( 'abcdefgh1234567890' );
$player->setEmail( 'amrun@dungeonfire.ch' );
$player->setPlayercount( 0 );
echo " OK<br />\n";

echo "<pre>\n";
print_r( $player );
echo "</pre>\n";


////////////////////////////////////////////////////////////////

$step++;
echo $step . ".) Spieler wird persistiert...";
$player->persist();
echo " OK<br />\n";

$player = null;


////////////////////////////////////////////////////////////////

$step++;
echo $step . ".) Spieler wird mittels der Id (1) ausgelesen...";
$player = Player::getById( 1 );
if( $player != null ) {
	echo " OK<br />\n";
	echo "<pre>\n";
	print_r( $player );
	echo "</pre>\n";
}
else {
	echo "NOK<br />\n";
	die();
}

////////////////////////////////////////////////////////////////

$step++;
echo $step . ".) Player(s) werden mittels der Spiel-Id (1) ausgelesen...";
$players = Player::getByGameId( 1 );
if( $players != null && count( $players ) > 0 ) {
	echo " OK -> Anzahl gefundener Spieler: " . count( $players ) . "<br />\n";
	echo "<pre>\n";
	print_r( $players );
	echo "</pre>\n";
}
else {
	echo "NOK<br />\n";
	die();
}

////////////////////////////////////////////////////////////////


?>