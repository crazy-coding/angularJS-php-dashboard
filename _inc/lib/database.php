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
class Database extends PDO
{
	public $log = NULL;
	public $db = NULL;
	public $statement = NULL;
	public $option = NULL;

   	public function __construct($dsn, 
                               $username=null, 
                               $password=null, 
                               $driver_options=array())
   	{
   		$this->log = new Log('sql.txt');
   		$default_options = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ];
        $options = array_replace($default_options, $driver_options);
      	parent::__construct($dsn, $username, $password, $options);
   	}

   	public function prepare($statement, $option = array())
	{
		$this->statement = $statement;
		$this->option = $option;
		$this->db = parent::prepare($this->statement, $this->option);
		return($this);
	}

	public function execute($args = null)
	{
		if (SYNCHRONIZATION) {
			if (
				(
				
				strlen(strstr($this->statement,'INSERT'))>0 
				|| strlen(strstr($this->statement,'UPDATE'))>0 
				|| strlen(strstr($this->statement,'DELETE'))>0
				
				)&&(

				!strlen(strstr($this->statement,"UPDATE `users` SET `ip` = ? WHERE `id` = ?")) > 0

				)
			) {
				
			    $this->log->simplyWrite($this->statement.'|'.serialize($args));
			}
		}
		
		$this->db->execute($args);
	}

	public function fetch($constant) 
	{
		return $this->db->fetch($constant);
	}

	public function fetchAll($constant) 
	{
		return $this->db->fetchAll($constant);
	}

	public function rowCount() 
	{
		return $this->db->rowCount();
	}

	public function lastInsertId($seqname = NULL) 
	{
		return parent::lastInsertId($seqname);
	}
}