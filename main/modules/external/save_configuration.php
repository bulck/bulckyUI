<?php

require_once('../../libs/config.php');
require_once('../../libs/utilfunc.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/utilfunc_sd_card.php');


$main_error = array();

$parttosave = getvar("parttosave");

// Regulation is not available for lamp and other
switch ($parttosave) 
{
    case "email":

        $EMAIL_PASSWORD = getvar("EMAIL_PASSWORD");
    
        $toSave = array(
            "EMAIL_PROVIDER" => getvar("EMAIL_PROVIDER"),
            "EMAIL_SMTP"     => getvar("EMAIL_SMTP"),
            "EMAIL_PORT"     => getvar("EMAIL_PORT"),
            "EMAIL_ADRESS"   => getvar("EMAIL_ADRESS"),
            "EMAIL_USE_SSL"   => getvar("EMAIL_USE_SSL")
        ); 
    
        // User set a new password
        if(strcmp("$EMAIL_PASSWORD","")!="") {
            $toSave["EMAIL_PASSWORD"] = $EMAIL_PASSWORD;
        }

        // Save configuration in DB
        configuration\saveEmailUserConf($toSave);
        
        // Write it in XML
        configuration\serverEmail_createXMLConf();
        
        break;
        
    case "supervision":
        $toSave = array(
            "checkPing_en"      => getvar("checkPing_en"),
            "checkPing_adress"  => getvar("checkPing_adress"),
            "checkPing_action"  => getvar("checkPing_action"),
            "dailyReport_en"    => getvar("dailyReport_en"),
            "monthlyReport_en"  => getvar("monthlyReport_en")
        );
        
        // Save configuration in DB
        cultipi\saveSupervisionUserConf($toSave);
        
        // Write in XML
        cultipi\serverSupervision_createXMLConf();
        
        break;
        
    default:
        break;
}



?>
