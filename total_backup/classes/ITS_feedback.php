<?php
/*=====================================================================//
ITS_feedback- query DB for concept browser.

Constructor: ITS_feedback()

ex. $query = new ITS_feedback($uid,$qid);

Last Revision: Greg Krudysz, Jul-20-2013
//=====================================================================*/

class ITS_feedback
{
	public $id;
    public $db_user;
    public $db_pass;
    public $db_host;
    public $db_name;
    public $debug;
    
    //=====================================================================//
    function __construct($id)
    {
        //=====================================================================//
        //echo 'NOW'; die();
        $this->debug = FALSE; //TRUE;
        
        if ($this->debug) {
            echo '<br>' . get_called_class();
        }
        global $db_dsn, $db_name, $tb_name, $db_table_user_state, $tex_path;
        
        $dsn = preg_split("/[\/:@()]+/", $db_dsn);
        //foreach ($dsn as $value) {echo $value.'<br>';}
        
        $this->id      = $id;
        $this->db_user = $dsn[1];
        $this->db_pass = $dsn[2];
        $this->db_host = $dsn[4];
        $this->db_name = $dsn[6];
        $this->tb_user = $db_table_user_state;
        // echo "Values: ".$this->db_host.$this->db_user.$this->db_pass;
    }
    //=====================================================================//
    function main($concept,$tag_id)
    {
        //=====================================================================//    
            
        $str = 'main here';
        
        return $str;
    }
     //=====================================================================//
    function render($qid,$tid)
    {
        //=====================================================================//    
        // Users pull-down menu
        $dialog = '<textarea name="message" rows="2" cols="50"></textarea>
					<input type="submit" value="Send">';
					
		$str = '<div id="feedbackContainerToggle" class="Question_Toggle"><span>&raquo;&nbsp;Feedback</span></div>'.
                '<div id="feedbackContent" style="display:none"><center>'. $dialog.'</center></div>';

        return $str;
    }   
    //=====================================================================//
} // eof class
?>
