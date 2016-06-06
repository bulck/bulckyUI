<?php

require_once('utilfunc_cultipi.php');


// {{{ get_error_sd_card_update_message()
// ROLE transoform a check sd card configuration code into a an error message
// IN   $id    id of the message
// RET "" if there is no message to display, the message else
function get_error_sd_card_update_message($id=0) {
    switch($id) { //Id to check to get the current error message:
        case ERROR_COPY_FILE:  return __('ERROR_COPY_FILE');
        case ERROR_WRITE_PROGRAM:  return __('ERROR_WRITE_PROGRAM');
        case ERROR_COPY_FIRM:  return __('ERROR_COPY_FIRM');
        case ERROR_COPY_PLUGA:  return __('ERROR_COPY_PLUGA');
        case ERROR_COPY_PLUG_CONF:  return __('ERROR_COPY_PLUG_CONF');
        case ERROR_COPY_TPL:  return __('ERROR_COPY_TPL');
        case ERROR_COPY_INDEX:  return __('ERROR_COPY_INDEX');
        case ERROR_WRITE_SD_CONF: return __('ERROR_WRITE_SD_CONF');
        case ERROR_WRITE_SD: return __('ERROR_WRITE_SD');
        case ERROR_COPY_PLGIDX: return __('ERROR_COPY_PLGIDX');
        default: return "";
    }
}
// }}}


// {{{ save_program_on_sd()
// ROLE write programs into the sd card
// IN   $sd_card        path to the sd card to save datas
//      $program        the program to be save in the sd card 
// RET true if data correctly written, false else
function save_program_on_sd($file,$program) {
    $out=array();

    // Init out program file contants
    $prog="";
    $nbPlug=count($program);
    $shorten=false;

    // Check if there are some plugs
    if($nbPlug == 0)
        return false;
   
    // Limit nb plugs to max allowed
    if(get_configuration("REMOVE_1000_CHANGE_LIMIT",$out)=="False") {
        if($nbPlug>$GLOBALS['PLUGV_MAX_CHANGEMENT']) {
            $nbPlug=$GLOBALS['PLUGV_MAX_CHANGEMENT'];
            $shorten=true;
        }
    } 

    // Complet nbPlug variable up to 3 digits
    while(strlen($nbPlug)<5)
        $nbPlug="0$nbPlug";
    
    // Add header of the file
    $prog=$nbPlug."\r\n";

    // If we have to reduce number of change
    if($shorten) {
        // For each line of the program add it to file
        for($i=0; $i<$nbPlug-1; $i++) 
            $prog=$prog."$program[$i]"."\r\n";
    } else {
        for($i=0; $i<$nbPlug; $i++) 
            $prog=$prog."$program[$i]"."\r\n";
    }

    // If the programm has been cut (too many change) add an last entry
    if($shorten) {
        $last=count($program)-1;
        $prog=$prog."$program[$last]"."\r\n";
    }

    // Write it on SD card
    if($f = @fopen($file,"w+")) {
        if(!@fwrite($f,"$prog")) 
        { 
            fclose($f);
            return false;
        }
            fclose($f);
    }

   return true;
}
// }}}


// {{{ compare_program()
// ROLE compare programs and data to check if they are up to date
// IN   $data         array containing datas to check
//      $sd_card      sd card path to save data
//      $file         file to be compared to
// RET false is there is something to write, true else
function compare_program($data,$file) {

    $out=array();

    if(is_file("${file}")) {
        $nb=0;
        //On compte le nombre d'entrée dans la base des programmes:
        $nbdata=count($data);

        //Si les changements de la base dépassent ceux de maximum définit, on coupe le tableau des programmes pour le faire
        //correspondre au nombre maximal
        if(get_configuration("REMOVE_1000_CHANGE_LIMIT",$out)=="False") {
            if($nbdata>$GLOBALS['PLUGV_MAX_CHANGEMENT']) {
                $tmp_array=array_slice($data, 0, $GLOBALS['PLUGV_MAX_CHANGEMENT']-1);
                $tmp_array[]=$data[$nbdata-1];
                $data=$tmp_array;
                $nbdata=count($data);
            }
        }

        
         while(strlen($nbdata)<5) {
            $nbdata="0$nbdata";
         }

        if(count($data)>0) {
            //On récupère les informations du fichier courant plugv
            $buffer_array=@file("$file");
            foreach($buffer_array as $buffer) {
                  $buffer=trim($buffer); //On supprime les caractères invisibles
                  if(!empty($buffer)) {
                     if($nb==0) {
                        if(strcmp("$nbdata","$buffer")!=0) { //S'il s'agit de la première ligne, qui contient le nombre d'entrée, on compare le nombre d'entrée du fichier avec le nombre d'entrée du tableau
                         return false;
                        }
                     } else {
                        if(strcmp($data[$nb-1],$buffer)!=0) { //Sinon on compare le contenu du fichier et celui de la ligne correspondante dans le tableau
                           return false;
                        }
                     }
                     $nb=$nb+1;
                  } else if($nb==0) {
                    return false;
                  }
            }
            return true; //Tout est égal, on renvoie true
        }
    }
    return false;
}
// }}}


// {{{ create_plgidx()
// ROLE create plgidx file
// IN  $data            data to write into the sd card (come from calendar\read_event_from_db )
// RET array containing plgidx
function create_plgidx($data) {
    $plgidx = array();
    $return=array();

    // If there is not event , return false
    if(count($data) == 0) 
        return $return;

    // Open database connexion
    $db = \db_priv_pdo_start();
    
    // Foreach event
    foreach($data as $event)
    {
        // If this is a program index event
        if ($event['program_index'] != "")
        {

            // Query plugv filename associated
            try {
                $sql = "SELECT plugv_filename FROM program_index WHERE id = \"" . $event['program_index'] . "\";";
                $sth = $db->prepare($sql);
                $sth->execute();
                $res = $sth->fetch();
            } catch(\PDOException $e) {
                $ret=$e->getMessage();
            }
        
            //
            $today = strtotime(date("Y-m-d"));
            $nextYear  = strtotime("+1 year", strtotime(date("Y-m-d")));
        
            // Start date
            $date = $event['start_year'] . "-" . $event['start_month']  . "-" . $event['start_day'];
            // End date
            $end_date = $event['end_year'] . "-" . $event['end_month']  . "-" . $event['end_day'];
            
            while (strtotime($date) <= strtotime($end_date)) {

                // Save only for futur element
                if (strtotime($date) >= $today && strtotime($date) < $nextYear)
                    $plgidx[$date] = $res['plugv_filename'];
                  
                // Incr date                  
                $date = date ("Y-m-d", strtotime("+1 day", strtotime($date)));
            }
        }
    }

    // Close connexion
    $db = null;
    
    // For each day
    for ($month = 1; $month <= 13; $month++) 
    {
        for ($day = 1; $day <= 31; $day++) 
        {
            // Format day and month
            $monthToWrite = $month;
            if (strlen($monthToWrite) < 2) {
                $monthToWrite="0$monthToWrite";
            }
            
            $dayToWrite = $day;
            if (strlen($dayToWrite) < 2) {
                $dayToWrite="0$dayToWrite";
            }
            
            // Date to search in event
            $dateToSearch = date("Y") . "-" . $monthToWrite . "-" . $dayToWrite;
            
            $plugvToUse = "00";
            if (array_key_exists($dateToSearch, $plgidx)) {
                $plugvToUse = $plgidx[$dateToSearch];
                    
                if (strlen($plugvToUse) < 2)
                    $plugvToUse = "0$plugvToUse";
            }

            // Write the day
            $return[]=$monthToWrite . $dayToWrite . $plugvToUse;
        }
    }
    return $return;
}
//}}}


?>
