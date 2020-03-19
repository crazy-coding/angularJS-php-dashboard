<?php
/*
| -----------------------------------------------------
| PRODUCT NAME: 	MODERN POS
| -----------------------------------------------------
| AUTHOR:			ITSOLUTION24.COM
| -----------------------------------------------------
| EMAIL:			info@itsolution24.com
| -----------------------------------------------------
| COPYRIGHT:		RESERVED BY ITSOLUTION24.COM
| -----------------------------------------------------
| WEBSITE:			http://itsolution24.com
| -----------------------------------------------------
*/
class Log 
{
	private $handle;
	
	public function __construct($filename) {
		$this->handle = fopen(DIR_LOG . $filename, 'a');
	}
	
	public function write($message) {
		fwrite($this->handle, date('Y-m-d G:i:s') . ' - ' . print_r($message, true) . "\n");
	}

	public function simplyWrite($message) {
		fwrite($this->handle, print_r($message, true) . "\n");
	}
	
	public function __destruct() {
		fclose($this->handle);
	}
}