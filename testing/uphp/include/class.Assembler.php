<?php
/**
 * assembles the whole html page and prints it
 * @author amrun
 */
class Assembler {
	/**
	 *
	 * @var simplexmlObject configuration of the uphp framework
	 */
	protected $conf;
	
	protected $additionalCSS = array();
	
	protected $additionalJS = array();
	
	/**
	 * constructor for class Assembler
	 *
	 * @param simplexmlObject $conf
	 */
	function __construct( $conf ) {
		$this->conf = $conf;
	}
	
	/**
	 *
	 * @param string $content the content to wrap
	 */
	function assemble( $content, $pageTitle = false, $mergePageTitle = false ) {
		$template = file_get_contents( $this->conf->path->uphpRoot . 'config/template/template.html' );
		
		// replace pagetitle
		if( $mergePageTitle == true && $pageTitle != false ) {
			$template = str_replace( '###PAGETITLE###', $this->conf->html->pageTitle . ' - ' . $pageTitle, $template );
		} elseif( $mergePageTitle == false && $pageTitle != false ) {
			$template = str_replace( '###PAGETITLE###', $pageTitle, $template );
		} else {
			$template = str_replace( '###PAGETITLE###', $this->conf->html->pageTitle, $template );
		}
		
		// replace js
		$template = str_replace( '###JS###', $this->loadJS(), $template );
		
		// replace css
		$template = str_replace( '###CSS###', $this->loadCSS(), $template );
		
		// replace content
		print str_replace( '###CONTENT###', $content, $template );
	
	}
	
	/**
	 * assembles the css part of the header and returns it as string
	 * @return string to include in header
	 */
	function loadCSS() {
		$first = true;
		$cssPart = '';
		foreach( $this->conf->css->file as $file ) {
			if( ! $first ) {
				$cssPart .= chr( 9 );
			}
			$cssPart .= '<link rel="stylesheet" type="text/css" media="all" href="';
			$cssPart .= $this->conf->path->baseUrl . $this->conf->path->css . $file;
			$cssPart .= '" />' . chr( 10 );
			$first = false;
		}
		
		if( count( $this->additionalCSS ) >= 1 ) {
			foreach( $this->additionalCSS as $key => $value ) {
				$cssPart .= '<style type="text/css">' . chr( 10 );
				$cssPart .= $value . chr( 10 );
				$cssPart .= '</style>' . chr( 10 );
			}
		
		}
		return $cssPart;
	}
	
	/**
	 * assembles the js part of the header and returns it as string
	 * @return string to include in header
	 */
	function loadJS() {
		$first = true;
		$jsPart = '';
		foreach( $this->conf->js->file as $file ) {
			if( ! $first ) {
				$jsPart .= chr( 9 );
			}
			$jsPart .= '<script type="text/javascript" src="';
			$jsPart .= $this->conf->path->baseUrl . $this->conf->path->js . $file;
			$jsPart .= '"></script>' . chr( 10 );
			$first = false;
		}
		
		if( count( $this->additionalJS ) >= 1 ) {
			foreach( $this->additionalJS as $key => $value ) {
				$jsPart .= '<script type="text/javascript">' . chr( 10 );
				$jsPart .= $value . chr( 10 );
				$jsPart .= '</script>' . chr( 10 );
			}
		}
		
		return $jsPart;
	}
	
	public function additionalCSS( $code ) {
		$this->additionalCSS[] = $code;
	}
	
	function additionalJS( $code ) {
		$this->additionalJS[] = $code;
	}
}
?>