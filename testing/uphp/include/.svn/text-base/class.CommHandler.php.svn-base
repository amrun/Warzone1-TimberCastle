<?php
/**
 * class CommHandler
 * provides functions for connecting to a mysql-db,
 * create and destroy php-sessions
 *
 * @author amrun
 *
 */
class CommHandler {
	
	// objectVars
	/**
	 * mysql connection of this instance
	 * @var unknown_type
	 */
	protected $mysqlConnection = null;
	
	/**
	 * @var conf
	 */
	protected $conf;
	
	/**
	 * constructor
	 *
	 * @param $configuration configuration-array of the uphp framework
	 */
	function __construct( $conf ) {
		$this->conf = $conf;
	}
	
	

	/**
	 * opens a new mysql link, if none has been established before
	 *
	 * @return opened mysql connection link of false if failed
	 */
	function openMysqlConnection() {
		if( $this->mysqlConnection == null ) {
			
			$this->mysqlConnection = mysql_connect( $this->conf->sql->localhost, $this->conf->commHandler->sql->username, $this->conf->commHandler->sql->password );
			
			if( ! $this->mysqlConnection ) {
				die( 'Could not connect: ' . mysql_error() );
			}
			
			mysql_select_db( $this->conf->commHandler->sql->database, $this->mysqlConnection );
		}
		
		return $this->mysqlConnection;
	}
	
	/**
	 * @return boolean true if connection has been closed, and false if there wasn't a connection open to close
	 */
	function closeMysqlConnection() {
		if( $this->mysqlConnection != null ) {
			return mysql_close( $this->mysqlConnection );
		}
	}
	
	/**
	 * executes a mysql query using the existing
	 *
	 * @param string $queryString
	 * @return array containing the result of the string
	 */
	function mysqlQuery( $queryString ) {
		$uphp = uphp::getInstance();
		$result = mysql_query( $queryString, $this->mysqlConnection );
		
		if( !$result ) {
			$logMessage = 'query-error. query: ';
			$uphp->log( $logMessage . $queryString, 'warning' );
		}
		
		return $result;
	}
	
	
	/**
	 * Sets up a query and returns the values in an associative array
	 *
	 * @param string $table Tablename
	 * @param string $field Fields to retreive
	 * @param string $where Where clause to restrict entries.
	 * @return Entries filled in an associative array.
	 */
	function mysqlSelect( $table, $fields, $where ) {
		$queryString = 'SELECT ' . $fields . ' FROM ' . $table . ' WHERE ' . $where;
		
		$res = $this->mysqlQuery( $queryString );
		$resultArray = array();
		
		while( $val = mysql_fetch_assoc( $res ) ) {
			$resultArray[] = $val;
		}
		
		return $resultArray;
	}
	
	
	/**
	 * Counts entries in given table.
	 *
	 * @param string $table Tablename
	 * @param string $field Fieldname to count
	 * @param string $where Where clause to restrict entries.
	 * @return Count of entries.
	 */
	function mysqlCount( $table, $field, $where ) {
		$ret = -1;
		$uphp = uphp::getInstance();
		$queryString = 'SELECT COUNT( ' . $field . ' ) FROM ' . $table . ' WHERE ' . $where;
		$result = $this->mysqlQuery( $queryString );

		if( $result ) {
			$ret = mysql_result( $result, 0 );
		} else {
			$logMessage = 'query-error. query: ';
			$uphp->log( $logMessage . $queryString, 'warning' );
		}
		
		return $ret;
	}
	
	
	/**
	 * Truncates the given table in MySQL.
	 *
	 * @param string $table
	 * @return true if successfuly otherwise false.
	 */
	function mysqlTruncate( $table ) {
		$ret = false;
		$uphp = uphp::getInstance();
		$queryString = 'TRUNCATE ' . $table;
		$result = $this->mysqlQuery( $queryString );

		if( $result ) {
			$ret = true;
		} else {
			$logMessage = 'query-error. query: ';
			$uphp->log( $logMessage . $queryString, 'warning' );
		}
		
		return $ret;
	}
	
	
	/**
	 * Sends a mail
	 *
	 * @param string $toMail Recipient of mail
	 * @param string $subject Subject of mail
	 * @param string $message Message body of mail
	 * @return true if successfuly otherwise false.
	 */
	function sendMail( $toMail, $subject, $message ) {
		$uphp = uphp::getInstance();
		
		if( $this->conf->commHandler->mail->logOutgoing ) {
			$logMessage = $this->conf->commHandler->mail->logMessage;
			$logMessage = str_replace( '###SUBJECT###', $subject, $logMessage );
			$logMessage = str_replace( '###MESSAGE###', $message, $logMessage );
			$uphp->log( $logMessage );
		}
		
		return mail( $to, $subject, $message );
	}
	
/*
 * 		TODO
	 // session handling


	 function createSession () {
		}

		function destroySession () {
		}
	*/
}
?>