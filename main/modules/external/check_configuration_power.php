<?php 

require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/config.php');

$message="";
if((isset($_GET['list_power']))&&(!empty($_GET['list_power']))) {
    $list=explode("-",$_GET['list_power']);
    $main_error=array();
    $nb=array();
    foreach($list as $plug) {
        if(!check_configuration_power($plug,$main_error)) {
              $nb[]=$plug;
        }
    }   

    if(count($nb)>0) {
        if(count($nb)==1) {
            $message=__('ERROR_POWER_PLUG')." ".$nb[0]." ".__('UNCONFIGURED_POWER')." ".__('CONFIGURABLE_PAGE_POWER')." <a href='/bulcky/index.php?menu=programs&selected_plug=".$nb[0]."&plugs_dial=true' class='note_link' id='plug_pwr_link'>".__('HERE')."</a>";
        } else {
            $tmp_number="";
            foreach($nb as $number) {
               if(strcmp($tmp_number,"")!=0) {
                   $tmp_number=$tmp_number.", ";
                }
                $tmp_number=$tmp_number.$number;
            }
            $message=__('ERROR_POWER_PLUGS')." ".$tmp_number." ".__('UNCONFIGURED_POWER')." ".__('CONFIGURABLE_PAGE_POWER')." <a href='/bulcky/index.php?menu=programs&selected_plug=".$nb[0]."&plugs_dial=true' class='note_link'>".__('HERE')."</a>";
        }
    }
} else {
  $message="1";
  if((isset($_GET['single_power']))&&(!empty($_GET['single_power']))) {
    $power=$_GET['single_power'];
    $main_error=array();
    if(!check_configuration_power($power,$main_error)) {
        $message="1";
    } else {
        $message="0";       
    }
  }
}
echo json_encode("$message");

?>
