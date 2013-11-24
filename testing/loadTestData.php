<?php

require_once( '../uphp/class.uphp.php' );

$step = 0;
$uphp = uphp::getInstance();
$link = $uphp->comm->openMysqlConnection();

////////////////////////////////////////////////////

$step++;
echo $step . ".) Testdaten werden eingelesen... ";
$content = file_get_contents( './warzone1TestGameData.sql', true );
echo "OK<br />\n";

////////////////////////////////////////////////////

$step++;
echo $step . ".) Testdaten werden in Datenbank eingespielt...<br />\n";

$filteredContent = '';
$lines = explode( "\n", $content );

foreach( $lines as $line ) {
	$startsWith = substr( $line, 0, 2 );
	
	if( $startsWith != '/*' && $startsWith != '--' ) {
		$filteredContent .= trim( $line . "\n" );
	}
}

$sqlQueries = explode( ';', $filteredContent );

$resOK = true;

for( $i = 0; $i < count( $sqlQueries ); $i++ ) {
	if( $sqlQueries[$i] != "" && count( $sqlQueries[$i] ) > 0 ) {
		echo $step . "." . ( $i + 1 ) . ".) Verarbeite query: <br />\n";
		echo "<pre>\n" . $sqlQueries[$i] . "\n</pre>\n";
		
		if( mysql_query( $sqlQueries[$i], $link ) ) {
			echo "\nOK<br /><br />\n";
		} else {
			echo "\nNOK<br /><br />\n";
			
			echo "<pre>\n";
			mysql_error( $link );
			echo "</pre>\n";
		}
	}
}

?>