<?php

$step = 0;
require_once( '../uphp/class.uphp.php' );
require_once( '../include/class.Game.php' );

	public $status = null;
	public $turn = null;
	public $setName = null;
////////////////////////////////////////////////////////////////

$step++;
echo $step . ".) Tabelle game wird geleert... ";
$uphp = uphp::getInstance();
if( $uphp->comm->mysqlTruncate( 'game' ) ) {
	echo " OK<br />\n";
}
else {
	echo " NOK<br />\n";
	die();
}

////////////////////////////////////////////////////////////////

$step++;
echo $step . ".) Spiel wird erstellt... ";
$game = new Game();
$game->setStatus( 0 );
$game->setTurn( 1 );
$game->setSetName( 'set1' );
echo " OK<br />\n";

echo "<pre>\n";
print_r( $game );
echo "</pre>\n";


////////////////////////////////////////////////////////////////

$step++;
echo $step . ".) Spiel wird persistiert...";
$game->persist();
echo " OK<br />\n";

$game = null;


////////////////////////////////////////////////////////////////

$step++;
echo $step . ".) Spiel wird mittels der Id (1) ausgelesen...";
$game = Game::getById( 1 );
if( $game != null ) {
	echo " OK<br />\n";
	echo "<pre>\n";
	print_r( $game );
	echo "</pre>\n";
}
else {
	echo "NOK<br />\n";
	die();
}

////////////////////////////////////////////////////////////////



?>