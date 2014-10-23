<?php

class QueryManager
{
	private $_limitRowsPerQuery = 3000;
	private $_index = 1;
	private $_insertHeaderQuery = null; // Ej. 'INSERT INTO table (`data1`, `data2`, ....) VALUES'
	private $_query = null;
	private $_useSemicolon = false; // If is true, a ';' will be put at the end of each query
	
	public function __construct($insertHeaderQuery)
	{
		$this->_insertHeaderQuery = $insertHeaderQuery;
	}
	
	public function addQueryValues($queryValues)
	{
		if($this->_index == 1){
			if($this->_useSemicolon)
				$this->_query .= ";\n". $this->_insertHeaderQuery ."\n($queryValues)";
			else
				$this->_query .= $this->_insertHeaderQuery ."\n($queryValues)";
		}
		else if($this->_index < $this->_limitRowsPerQuery)
			$this->_query .= ",\n($queryValues)";
		
		$this->_index += 1;
		$this->_useSemicolon = true;
		
		if($this->_index == $this->_limitRowsPerQuery)
			$this->_index = 1;			
	}
	
	public function getQuery()
	{
		return $this->_query .';';
	}
	
	public function getHtmlQuery()
	{
		return str_replace("\n", '</br>', $this->_query .';');
	}
}