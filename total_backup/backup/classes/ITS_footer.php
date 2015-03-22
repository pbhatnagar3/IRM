<?php
/*=====================================================================//
ITS_footer - creates a footer.

Constructor: ITS_footer(name,rows,cols,data,width)

ex. $footer = new ITS_footer();

Author(s): Greg Krudysz |  Aug-24-2012
//=====================================================================*/

class ITS_footer
{
    public $id;
    public $term;
    
    //=====================================================================//
    function __construct($status, $date, $runtime){
    //=====================================================================//
        global $db_dsn, $db_name, $tb_name, $db_table_user_state;
        
        $this->status  = $status;
        $this->date    = $date;
        $this->runtime = $runtime;

        self::main($status, $date, $runtime);
    }
    //=====================================================================//
    function main(){
    //=====================================================================//
        $footer = '<div class="ITS_footer"><ul class="ITS_footer">' . '<li>Last Updated: ' . $this->date . '</li>';
        
        if (!empty($this->runtime)) {
            $footer .= '<li>Page created in ' . round($this->runtime, 2) . ' secs</li>';
        }
        $emails = $this->emails();
        $footer .= '<li>'.$emails.'</li>' . '<li></li></ul>' . '</div>';
        
        return $footer;
    }
    //=====================================================================//
    function emails(){
    //=====================================================================//
        $emails = 'krudysz<b>&Dagger;</b>ece.gatech.edu <b>+</b> jim.mcclellan<b>&Dagger;</b>ece.gatech.edu';
        
        return $emails;
    }
    //=====================================================================//
}

// eo:class
//=====================================================================//
?>
