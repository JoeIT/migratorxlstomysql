<?php
class LibFunctions
{
	public $mysqli = null;
	
	function __construct()
	{
		$this->mysqli = new mysqli("localhost", "root", "", "intranetssu");

		/* comprobar la conexión */
		if ($this->mysqli->connect_errno) {
			printf("Falló la conexión: %s\n", $this->mysqli->connect_error);
			exit();
		}
	}
	
	
	// Function that checks if a code is already registered in a table
	function verifyCode($code, $table, $field)
	{			
		// check whether the code is being used
		$result = $this->mysqli->query("SELECT ".$field." FROM ".$table." WHERE ".$field."='".$code."' LIMIT 1");
		$numusers = $result->num_rows;

		if ($numusers==0) return FALSE;
		else return TRUE;			
	}
	
	//*************************************************************************
	// Function that returns a random string.
	// $numcharacters: number of letters returned string will.
	// $withrepeated: if 0 returns a string with no letters repeated. 
	// $withrepeated: if 1 returns a string with repeated letters.
	function getCode($numcharacters,$withrepeated)
	{
		$code = '';
		$characters = "0123456789abcdfghjkmnpqrstvwxyzBCDFGHJKMNPQRSTVWXYZ";
		$i = 0;
		while ($i < $numcharacters) {
			$char = substr($characters, mt_rand(0, strlen($characters)-1), 1);	
			if ($withrepeated == 1) {
				$code .= $char;
				$i += 1;			
			} else {
				if(!strstr($code,$char)) {
					$code .= $char;
					$i += 1;
				}
			}
		}
		return $code;
	}
	
	// Function that seeks a 11 digit code that is not registered in a table.
	function uniqueCode($numcharacters, $withrepeated, $table, $field)
	{
		$code = $this->getCode($numcharacters, $withrepeated);
		while ($this->verifyCode($code, $table, $field)) {	
			$code = $this->getCode(11, 1);
		}
		return $code;
	}
}



