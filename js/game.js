window
		.addEvent(
				'domready',
				function() {
					
					$('sendPlayerMoveButton').set('disabled', true);
					
					var getGameStateRequest = new Request.JSON(
							{
								url: '###BASEURL###?stage=ajaxRequest&request=getGameState&playerHash=###PLAYERHASH###',
								onComplete: function( units ) {
									warzoneFunc.placeUnits( units );
								}
							} ).send();

				} );

var warzoneFunc = {

	playerMove: new Array(),

	pawnMoveId: null,

	pawnMoveStart: null,

	pawnMoveTarget: null,

	pawnMovesCount: 0,

	placeUnits: function( units ) {
		var i = 0;
		while( i < units.length ) {
			var j = 0;
			while( j < units[i].length ) {
				if( i == 0 ) {
					warzoneFunc.placeActiveUnit( units[i][j].xPos,
							units[i][j].yPos, units[i][j].name, units[i][j].id );
				} else {
					warzoneFunc.placePassiveUnit( units[i][j].xPos,
							units[i][j].yPos, units[i][j].name );
				}
				j++;
			}
			i++;
		}
	},

	placeActiveUnit: function( x, y, name, id ) {
		var tileId = 'x' + x + 'y' + y;
		$( tileId ).setStyle( 'background-image', "url('./graphics/pawn/" + name + ".png')" );
		$( tileId ).setStyle( 'background-repeat', "no-repeat" );
		$( tileId ).setStyle( 'background-position', "center center" );
		$( tileId ).setStyle( 'background-color', "#2eff3d" );
		$( tileId ).addEvent( 'click', function( e ) {
			e.stop();
			warzoneFunc.startMovePawn( id, $( tileId ) );
			warzoneFunc.processPawnDropzones( id );
		} );
	},

	placePassiveUnit: function( x, y, name ) {
		var tileId = 'x' + x + 'y' + y;
		$( tileId ).setStyle( 'background-image', "url('./graphics/pawn/" + name + ".png')" );
		$( tileId ).setStyle( 'background-repeat', "no-repeat" );
		$( tileId ).setStyle( 'background-position', "center center" );
		$( tileId ).setStyle( 'background-color', "#ff2e2e" );
	},

	startMovePawn: function( pawnId, position ) {
		warzoneFunc.pawnMoveId = pawnId;
		warzoneFunc.pawnMoveStart = position;
	},

	endMovePawn: function() {
		warzoneFunc.playerMove[warzoneFunc.pawnMovesCount] = new Array(
				warzoneFunc.pawnMoveId, warzoneFunc.pawnMoveTarget );
		warzoneFunc.pawnMovesCount++;
		warzoneFunc.replacePawn( warzoneFunc.pawnMoveStart.get( 'id' ),
				warzoneFunc.pawnMoveTarget );
		$('sendPlayerMoveButton').set('disabled', false);
	},

	sendPlayerMove: function() {
		$('sendPlayerMoveButton').set('disabled', true);
		warzoneFunc.setMessage( 'Der Spielzug wird übermittelt.', 'green' );
		var jsonMove = encodeURI(JSON.encode( warzoneFunc.playerMove ));
		
		var sendMove = new Request(
				{
					url: '###BASEURL###?stage=ajaxRequest&request=receiveMove&move=' + jsonMove,

					onSuccess: function( message ) {
						warzoneFunc.setMessage(
								'Der Spielzug wurde übermittelt. Sie werden in 5 sekunden weitergeleitet.', 'green' );
						setTimeout ( "location.reload()", 5000 );
								
					},

					onFailure: function( message ) {
						warzoneFunc
								.setMessage(
										'Der Spielzug konnte nicht übermittelt werden! Betätige den Reset-Button und führe deinen zug noch einmal aus.',
										'red' );
					}
				} ).send();
	},

	setMessage: function( message, color ) {
		$( 'messages' ).set( 'html', '' );
		$( 'messages' ).set( 'html', message );
		$( 'messages' ).setStyle( 'color', color );
	},

	processPawnDropzones: function( pawnId ) {
		warzoneFunc.deactivateDropzones();
		warzoneFunc.unmarkDropzones();
		var getDropzones = new Request.JSON(
				{
					url: '###BASEURL###?stage=ajaxRequest&request=getPawnDropzones&pawnId=' + pawnId,
					onComplete: function( dropzones ) {
						var filteredDropzones;
						filteredDropzones = warzoneFunc
								.filterDropzones( dropzones );
						warzoneFunc.activateDropzones( filteredDropzones );
						warzoneFunc.markDropzones( filteredDropzones );
					}
				} ).send();
	},

	replacePawn: function( from, to ) {
		$( from ).removeEvents( 'click' );
		var tmpStyle = $( from ).get( 'style' );
		$( from ).setStyle( 'background-image', '' );
		$( from ).setStyle( 'background-color', '#fffeb2' );

		$( to ).set( 'style', tmpStyle );
		$( to ).setStyle( 'background-color', '#fffeb2' );
		$( to ).erase( 'name' );
	},

	markDropzones: function( dropzones ) {
		var i = 0;

		while( i < dropzones.length ) {
			$( dropzones[i] ).setStyle( 'background-color', "#358bff" );
			i++;
		}
	},

	unmarkDropzones: function() {
		var activeElements = document.getElementsByName( "active" );
		var i = 0;
		while( i < activeElements.length ) {
			$( activeElements[i] ).setStyle( 'background-color', "" );
			i++;
		}
	},

	filterDropzones: function( dropzones ) {
		var newDropzones = Array();
		var i = 0;
		var count = 0;
		while( i < dropzones.length ) {
			var j = 0;
			var isValid = true;
			while( j < warzoneFunc.playerMove.length ) {
				if( $( dropzones[i] ).get( 'id' ) == warzoneFunc.playerMove[j][1] ) {
					isValid = false;
				}
				j++;
			}
			if( isValid ) {
				newDropzones[count] = dropzones[i];
				count++;
			}
			i++;
		}
		return newDropzones;

	},

	activateDropzones: function( dropzones ) {
		var i = 0;

		while( i < dropzones.length ) {
			$( dropzones[i] ).addEvent( 'click', function( e ) {
				e.stop();
				warzoneFunc.pawnMoveTarget = this.get( 'id' );
				warzoneFunc.deactivateDropzones( dropzones );
				warzoneFunc.endMovePawn();
			} );
			$( dropzones[i] ).set( 'name', 'active' );
			i++;
		}
	},

	deactivateDropzones: function() {
		var activeElements = document.getElementsByName( "active" );
		var i = 0;
		while( i < activeElements.length ) {
			activeElements[i].setStyle( 'background-color', "" );
			activeElements[i].removeEvents( 'click' );
			i++;
		}
	}
};