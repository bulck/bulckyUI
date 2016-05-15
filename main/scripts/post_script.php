<?php 
    // IN DEV : Add check sd card message
    if(get_configuration("STATISTICS",$main_error) == "True") {
        $send_stat=true;
    } else {
        $send_stat=false;
    }
    
    include '../../libs/js/check_sd.js';
  
?>
