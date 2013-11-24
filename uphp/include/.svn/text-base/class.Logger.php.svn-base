<?php
/**
 * class Logger
 * provides functions for logging messages into a file
 *
 * @author amrun
 */
class Logger {
	/**
	 *
	 * @var simplexmlObject configuration of the uphp framework
	 */
	protected $conf;
	
	/**
	 * variable containing the path to the logfiles
	 * @var array
	 */
	protected $logfilePath = array();
	
	function __construct( $conf ) {
		$this->conf = $conf;

		foreach( $this->conf->logger->file as $file ) {
			switch( (string) $file['type'] ) { // Get attributes as element indices
				case 'info':
					$this->logfilePath['info'] = $this->conf->path->uphpRoot . 'log/' . $file;
					break;
				case 'warning':
					$this->logfilePath['warning'] = $this->conf->path->uphpRoot . 'log/' . $file;
					break;
			}
		}
	}
	
	function log( $message, $severity ) {
		switch( $severity ) {
			case 'info':
				$this->logInfo( $message );
				break;
			case 'warning':
				$this->logSevere( $message );
				break;
		}
	}
	
	function logInfo( $message ) {
		$file = fopen( $this->logfilePath['info'], 'a' );
		fwrite( $file, date( $this->conf->logger->dateFormat ) . ': ' . $message . chr( 10 ) . chr( 10 ) );
		fclose( $file );
	}
	
	function logSevere( $message ) {
		$file = fopen( $this->logfilePath['warning'], 'a' );
		fwrite( $file, date( $this->conf->logger->dateFormat ) . ': ' . $message . chr( 10 ) . chr( 10 ) );
		fclose( $file );
	}
}
?>