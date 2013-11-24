<?php

$step = 0;
require_once( '../uphp/class.uphp.php' );
require_once( '../include/class.Pawn.php' );


////////////////////////////////////////////////////////////////

$step++;
echo $step . ".) Tabelle pawn wird geleert... ";
$uphp = uphp::getInstance();
if( $uphp->comm->mysqlTruncate( 'pawn' ) ) {
	echo " OK<br />\n";
}
else {
	echo " NOK<br />\n";
	die();
}

////////////////////////////////////////////////////////////////

$step++;
echo $step . ".) Pawn wird erstellt... ";
$pawn = new Pawn();
$pawn->setPlayerId( 1 );
$pawn->setGameId( 1 );
$pawn->setName( 'Knight' );
$pawn->setAttackRange( 10 );
$pawn->setBashArmor( 11 );
$pawn->setBashAttack( 12 );
$pawn->setPierceArmor( 12 );
$pawn->setPierceAttack( 13 );
$pawn->setHitpoints( 14 );
$pawn->setWalkRange( 15 );
$pawn->setXPos( 16 );
$pawn->setYPos( 17 );
echo " OK<br />\n";

echo "<pre>\n";
print_r( $pawn );
echo "</pre>\n";


////////////////////////////////////////////////////////////////

$step++;
echo $step . ".) Pawn wird persistiert...";
$pawn->persist();
echo " OK<br />\n";

$pawn = null;


////////////////////////////////////////////////////////////////

$step++;
echo $step . ".) Pawn wird mittels der Id (1) ausgelesen...";
$pawn = Pawn::getById( 2 );
if( $pawn != null ) {
	echo " OK<br />\n";
	echo "<pre>\n";
	print_r( $pawn );
	echo "</pre>\n";
}
else {
	echo "NOK<br />\n";
	die();
}

////////////////////////////////////////////////////////////////

$step++;
echo $step . ".) Pawn(s) werden mittels der Spieler-Id (1) ausgelesen...";
$pawns = Pawn::getByPlayerId( 1 );
if( $pawns != null && count( $pawns ) > 0 ) {
	echo " OK -> Anzahl gefundener Figuren: " . count( $pawns ) . "<br />\n";
	echo "<pre>\n";
	print_r( $pawns );
	echo "</pre>\n";
}
else {
	echo "NOK<br />\n";
	die();
}

////////////////////////////////////////////////////////////////


?>