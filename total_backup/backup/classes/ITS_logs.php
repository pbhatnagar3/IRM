<?php
//=====================================================================//
/*
  ITS_log class - Logs.

  Constructor: ITS_log(*)

  Methods: 
  * addToLog($tableToInsertInto, $relatedTable, $qid, $action);

  ex. $ITS_log = new ITS_log($id); <- This $id is session id
  * 
  
		-id				-Auto incrementing ID
		-question_id	- id of the resource 
		-tbname			- resource table the log entry is related to (i.e. tags/questions/solutions)
		-field			- field being changed within tbname
		-content		- content within field within tbname
		-author			- Session ID
		-timestamp		- time()
		-action			- action taken

  Author(s): Drew Boatwright  | June-18-2012
  Last Revision: June-18-2012, Drew Boatwright
*/
//=====================================================================//

class ITS_logs {
	
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
	
	function addtolog($q_id, $tabname, $tabfield, $text, $act){
		$qid 		= $q_id;					//question_id
		$tbname		= $tabname;					//tbname
		$tbfield 	= $tabfield;				//field
		$content	= $text;					//content
		$content 	= addslashes($content);		//content
		$action		= $act;						//action
		$logTable 	= 'logs';
		$query3 = 'INSERT INTO '.$logTable.' (question_id, tbname, field, content, author, timestamp, action) VALUES ('.$qid.', \''.$tbname.'\',\''.$tbfield.'\',\''.$content.'\', '.$this->sessionId.', '.time().', \''.$action.'\');';
		$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
		return;
	}
	
	

}
?>
