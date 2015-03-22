<?php
//=====================================================================//
/* GOOD ONE
  ITS_log class - Logs.

  Constructor: ITS_log(*)

  Methods: 
  * addToLog($tableToInsertInto, $relatedTable, $qid, $action);

  ex. $ITS_log = new ITS_log($id);

  Author(s): Drew Boatwright  | June-18-2012
  Last Revision: June-18-2012, Drew Boatwright
*/
//=====================================================================//

class ITS_log {
	
	function __construct($id){
	//=============================================================//
		global $db_dsn, $db_name, $tb_name, $host;
		
		$this->users_id = $id;
		
		$this->db_name = $db_name; //Database name
		$this->tb_name = $tb_name; //Table name
		$this->host = 'localhost'; //Host
		$dsn = preg_split("/[\/:@()]+/",$db_dsn);
		
		$this->db_user = $dsn[1]; //Database username
		$this->db_pass = $dsn[2]; //Database pass
		
		$this->con = mysql_connect($this->host,$this->db_user,$this->db_pass) or die('Could not Connect to DB');
		mysql_select_db($this->db_name, $this->con) or die('Could not select DB');
		
	}
	
	function addToLog($tableToInsertInto, $relatedTable, $qid, $action){
		$actionText = $action;
		$qNum = $qid;
		$logTable = $tableToInsertInto;
		$relTab = $relatedTable;
		$query3 = 'INSERT INTO '.$logtable.' (question_id, table_name, author, timestamp, actionTaken) VALUES ('.$qNum.', \''.$relTab.'\', '.$this->users_id.', '.time().', \''.$actionText.'\');';
		$res3 = mysql_query($query3) or die(''.mysql_error().' Adding into log');
		return;
	}
	
}
?>
