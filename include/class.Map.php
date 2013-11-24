<?php

/**
 * Diese Klasse dient zur Generierung des Spielfeldes.
 */
class Map {
	
	/**
	 * Diese Methode erstellt eine neue Instanz des Typs "Map".
	 */
	public function __construct() {
	}
	
	/**
	 * Diese Methode generiert das Spielfeld und gibt es als HTML String zurück.
	 *
	 * @param xCount Breite des Spielfeldes
	 * @param yCount Höhe des Spielfeldes
	 */
	public function generate( $xCount, $yCount ) {
		$tileSize = 16;
		$mapWidth = $xCount * ( $tileSize + 2 );
		$mapHeight = $yCount * ( $tileSize + 2 );
		
		$map = '<div id="mapContainer" class="map" style="width: ' . $mapWidth . 'px; height: ' . $mapHeight . 'px;">';
		$map .= "\n";
		
		while( $y < $yCount ) {
			$map .= '	<div class="mapRow">';
			$map .= "\n";
			
			while( $x < $xCount ) {
				$tileXPos = $x + 1;
				$tileYPos = $y + 1;
				
				$map .= '		<div id="x' . $tileXPos . 'y' . $tileYPos . '" style="width: ' . $tileSize . 'px; height: ' . $tileSize . 'px"';
				$map .= ' class="mapTile"';
				$map .= '><!-- --></div>';
				$map .= "\n";
				
				$x++;
			}
			
			//$map .= '		<div class="clear"><!-- --></div>';
			//$map .= "\n";
			$map .= '	</div>';
			$map .= "\n";
			
			$x = 0;
			$y++;
		}
		
		$map .= '</div>';
		$map .= "\n";
		
		return $map;
	}
}

?>