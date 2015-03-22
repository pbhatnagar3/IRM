<?php


class ITS_log{
	
	function __construct($id){
	//=============================================================//
		global $db_dsn, $db_name, $tb_name, $host;
		
		$this->sessionId = $id;
		
		$this->db_name = $db_name; //Database name
		$this->tb_name = $tb_name; //Table name
		$this->host = 'localhost'; //Host
		$dsn = preg_split("/[\/:@()]+/",$db_dsn);
		
		$this->db_user = $dsn[1]; //Database username
		$this->db_pass = $dsn[2]; //Database pass
		
		$this->con = mysql_connect($this->host,$this->db_user,$this->db_pass) or die('Could not Connect to DB');
		mysql_select_db($this->db_name, $this->con) or die('Could not select DB');
		
	}
	/*
	function addToLog($q_id, $tabname, $tabfield, $cont, $act){
		$qid 		= $q_id;
		$tbname		= $tabname;
		$tbfield 	= $tabfield;
		$content	= $cont;
		$action		= $act;
		$logTable 	= 'logs';
		$query3 	= 'INSERT INTO '.$logtable.' (question_id, tbname, field, content, author, timestamp, action) VALUES ('.$qid.', \''.$tbname'\', \''.$tbfield.'\', \''.$content.'\' '.$this->sessionId.', '.time().', \''.$action.'\');';
		$res3 		= mysql_query($query3) or die(''.mysql_error().' Adding into log');
		return;
	}
	*/
	function addtolog($q_id, $tabname, $tabfield, $text, $act){
		$qid 		= $q_id;
		$tbname		= $tabname;
		$tbfield 	= $tabfield;
		$content	= $text;
		$content 	= addslashes($content);
		$action		= $act;
		$logTable 	= 'logs';
		$query3 = 'INSERT INTO '.$logTable.' (question_id, tbname, field, content, author, timestamp, action) VALUES ('.$qid.', \''.$tbname.'\',\''.$tbfield.'\',\''.$content.'\', '.$this->sessionId.', '.time().', \''.$action.'\');';
		$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
		return;
	}
}

?>
