<?php
$uphpRoot = 'uphp/';

require_once ($uphpRoot . 'include/class.CommHandler.php');
require_once ($uphpRoot . 'include/class.Assembler.php');
require_once ($uphpRoot . 'include/class.Logger.php');
require_once ($uphpRoot . 'include/class.Toolbox.php');

/**
 * central singleton class. provides access to the whole functionality of uphp
 * 
 * @author amrun
 *
 */
class uphp {
	protected $uphpRoot = 'uphp/';
	

	protected static $_instance;
	
	// configuration array
	public $conf = array();
	
	// CommHandler Instance
	public $comm = null;
	
	// Logger Instance
	public $logger = null;
	
	// Assembler Instance
	public $assembler = null;
	
	// Toolbox Instance
	public $toolbox = null;
	
	/**
	 * constructor for the class uphp
	 */
	private function __construct() {
		// read config.xml
		$this->conf = simplexml_load_file( $this->uphpRoot . 'config/config.xml' );
		
		if( $this->comm == NULL ) {
			// instantiate CommHandler and open mysql connection
			$this->comm = new CommHandler( $this->conf, $this );
			$this->comm->openMysqlConnection();
		}
		
		$this->assembler = new Assembler( $this->conf );
		$this->toolbox = new Toolbox();
	}
	
	public static function getInstance() {
		if( self::$_instance === NULL ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * calls the assembler and prints the page
	 * 
	 * @param string $content content for the html page
	 * @param string $pageTitle pagetitle to set for the page
	 * @param boolean $mergePageTitle set if the given pagetitle is
	 * to be merged with the one in the template or replaces it
	 */
	function assembleHtml( $content, $pageTitle = false, $mergePageTitle = false ) {
		$this->assembler->assemble( $content, $pageTitle, $mergePageTitle );
		exit();
	}
	
	/**
	 * wrapperfunction for the logger
	 * 
	 * @param string $message message to log
	 * @param string $severity severity of the log
	 */
	function log( $message, $severity = 'info' ) {
		if( $this->logger == null ) {
			// instantiate logger
			$this->logger = new Logger( $this->conf );
		}
		
		$this->logger->log( $message, $severity );
	}

}
?>
