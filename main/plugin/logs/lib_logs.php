<?php

namespace logs {

// {{{ check_db()
// ROLE check and update database
// RET none
function check_db() {

    // Check if table configuration exists
    $sql = "SHOW TABLES FROM bulcky LIKE 'logs';";

    $db = \db_priv_pdo_start("root");
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    // If table exists, return
    if ($res == null)
    {

        // Buil MySQL command to create table
        $sql = "CREATE TABLE logs ("
            ."timestamp varchar(14) NOT NULL DEFAULT '',"
            ."record1 int(4) DEFAULT NULL,"
            ."record2 int(4) DEFAULT NULL,"
            ."date_catch varchar(10) DEFAULT NULL,"
            ."time_catch varchar(10) DEFAULT NULL,"
            ."fake_log varchar(5) NOT NULL DEFAULT 'False',"
            ."sensor_nb int(4) NOT NULL DEFAULT '1',"
            ."KEY timestamp (timestamp));";

        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
    }
    $db = null;
    
    // Check the second table 
    $sql = "SHOW TABLES FROM bulcky LIKE 'bpilogs';";

    $db = \db_priv_pdo_start("root");
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res = $sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }

    // If table exists, return
    if ($res == null)
    {

        // Buil MySQL command to create table
        $sql = "CREATE TABLE bpilogs ("
            ."timestamp DATETIME NOT NULL,"
            ."sensor1 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor2 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor3 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor4 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor5 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor6 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor7 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor8 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor9 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor10 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor11 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor12 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor13 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor14 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor15 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor16 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor17 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor18 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor19 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor20 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor21 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor22 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor23 DECIMAL(6,2) DEFAULT NULL,"
            ."sensor24 DECIMAL(6,2) DEFAULT NULL,"
            ."KEY timestamp (timestamp));";

        // Create table
        try {
            $sth = $db->prepare($sql);
            $sth->execute();
        } catch(\PDOException $e) {
            $ret = $e->getMessage();
            print_r($ret);
        }
    }
    $db = null;
    
}


// {{{ export_table_csv()
// ROLE export a program into a text file
// IN $name       name of the table to be exported
//    $datefrom   start date to export logs
//    $dateto     end date to export logs
//    $out         error or warning message
// RET none
function export_table_csv($name="",$datefrom="",$dateto="",&$out) {
    if($name == "")
        return 0;

    $file="../../../tmp/export/$name.csv";

    if(is_file($file)) {
        unlink($file);
    }

    if(strcmp("$name","logs")==0) {
        $field="timestamp, record1, record2, date_catch, time_catch, fake_log, sensor_nb";
    } else {
        $field="timestamp, record,  plug_number, date_catch, time_catch";
    }

    if((strcmp("$datefrom","")!=0)&&(strcmp("$dateto","")!=0)) {
        $where='WHERE date_catch BETWEEN "'.$datefrom.'" AND "'.$dateto.'"';
    }    

    exec("/usr/bin/mysql --defaults-extra-file=/var/www/bulcky/sql_install/my-extra.cnf -B -h 127.0.0.1 --port=3891 bulcky -e 'SELECT ${field} FROM `${name}` ${where}' > $file");
}
// }}}



// {{{ check_export_table_csv()
// ROLE check that a table is empty or not
// IN $name       name of the table to be exported
//    $out         error or warning message
// RET false is table is empty, true else
function check_export_table_csv($name="",&$out) {
    if($name == "")
        return false;

    if($name == "logs") {
        $sql = "SELECT timestamp FROM {$name} WHERE fake_log LIKE 'False' LIMIT 1;";
    } else if($name == "power") {
        $sql = "SELECT timestamp FROM {$name} LIMIT 1;";
    } else {
        $sql = "SELECT * FROM {$name} LIMIT 1;";
    }

    $db = \db_priv_pdo_start();
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res=$sth->fetch(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if(count($res)>0) {
        if($res['timestamp'] != "")
            return true;
    }
    
    return false;
}
// }}}

// {{{ get_sensor_db_type()
// ROLE get list of sensor's type and definition from database and config file
// IN none
// RET array containing sensors type
// Type => nom abrégé => Facteur multiplicatif => unité
// '0' => 'none' => N/A => pas d'unité
// '2' => 'temp_humi' => 100 => °C ou % 
// '3' => 'water_temp' => 100 => °C
// '5' => 'wifi' => N/A => N/A
// '6' => 'water_level' => 100 => cm
// '8' => 'ph' => 100 => Pas d'unité
// '9' => 'ec' => 1 => µs/cm
// ':' => 'od' => 100 => mg/l
// ';' => 'orp' => 1 => mV 
// '10' => CO²  => 1 => ppm 
// '11' => Pression  => 100 => bar 
// '12' => Humidité  => 100 => % 

function get_sensor_db_type($sensor = "",$sensor_list) {
    if ($sensor == "") {
        $sql = "SELECT * FROM sensors ORDER BY id ASC;";
    } else { 
        $sql = "SELECT * FROM sensors WHERE id = '{$sensor}';";
    }

    $db = \db_priv_pdo_start();
    $res="";
    try {
        $sth=$db->prepare($sql);
        $sth->execute();
        $res=$sth->fetchAll(\PDO::FETCH_ASSOC);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    $nb_sens=1;

    $sensors=array();

    foreach($res as $sens) {
        if((array_key_exists($sens['type'],$sensor_list))&&(strcmp($sens['type'],"0"))) {
            $tmp_sen=$sensor_list["${sens['type']}"];
            $tmp_sen["id"]=$sens['id'];
            $tmp_sen["sensor_nb"]=$nb_sens;
            $sensors[]=$tmp_sen;
            
            $nb_sens=$nb_sens+1;
        } else {
             $tmp_sen=$sensor_list["${sens['type']}"];
             $tmp_sen["id"]=$sens['id'];
             $tmp_sen["sensor_nb"]=0;
             $sensors[]=$tmp_sen;

        }
    }
    return $sensors;
}
/// }}}

// {{{ get_sensor_log()
// ROLE Retrieve logs of a sensor
// IN sensor Sensor number
// IN dateStart Date to start (write in Unix (s) format) . Time is not used
// IN dateEnd   Date to start (write in Unix (s) format) . Time is not used
// IN day : Define if we want a day view or a month view
// IN ratio : define the diviser for each record
// RET array with logs value
function get_sensor_log ($sensor, $dateStart, $dateEnd, $day="day",$ratio=100,$display="logs") {

    // Init return array
    $serie = array();
    $serie[0] = array();
    $serie[1] = array();

    // Open connection to dabase
    $db = \db_priv_pdo_start();

    // Select correct divider
    if ($day == "day")
        $divider = 55;
    else
        $divider = 1200;
        
    // Get all point bewteen two dates
    if($day=="day") {
        $dateStartForLogsTable  = date ("ymd", $dateStart);
    } else {
        $dateStartForLogsTable  = date ("ym", $dateStart);
    }

    $sql = "SELECT timestamp , time_catch, date_catch, record1 , record2 FROM logs"
            . " WHERE sensor_nb = {$sensor}"
            . " AND timestamp LIKE '{$dateStartForLogsTable}%'"
            . " ORDER BY date_catch,time_catch ASC;";

    try {
        $sth = $db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        print_r($e->getMessage());
    }

    $lastTimeInS = 0;
    $oldRecord1 = 0;
    $oldRecord2 = 0;

    // For each point
    while ($row = $sth->fetch()) 
    {
        // Format date
        $realDate = $row['date_catch'] . " " . wordwrap($row['time_catch'],"2",":");
        date_default_timezone_set('Europe/Paris');
        $realTimeInS = strtotime($realDate);
        date_default_timezone_set('UTC'); 

        // Don't display all point. Only if they have suffisiant diff
        $first=false;
        if ($realTimeInS >= ($lastTimeInS + $divider))
        {

            //If there is a change state and display like program, add a point to the same time:
            if(strcmp("$display","program")==0) {
                if(($oldRecord1!=$row['record1'])&&(!isset($serie[(string)(1000 * ($realTimeInS))-1]))) {
                    $serie[0][(string)((1000 * ($realTimeInS))-1)] = $oldRecord1 / $ratio;
                }


                if(($row['record2'] != "") &&($row['record2'] != null)) {
                  if(($oldRecord2!=$row['record2'])&&(!isset($serie[(string)(1000 * ($realTimeInS))-1]))) {
                    $serie[1][(string)((1000 * ($realTimeInS))-1)] = $oldRecord2 / $ratio;
                    }
                }
            }

            // WTF ! 7200
            $serie[0][(string)(1000 * ($realTimeInS))] = $row['record1'] / $ratio;
            
            if ($row['record2'] != "" && $row['record2'] != null)
                $serie[1][(string)(1000 * ($realTimeInS))] = $row['record2'] / $ratio;
            
            $lastTimeInS = $realTimeInS;
            $oldRecord1=$row['record1'];
        
             if ($row['record2'] != "" && $row['record2'] != null)
                 $oldRecord2=$row['record2'];
        }
    }
    return $serie ;
}
// }}}


// {{{ are_fake_logs()
// ROLE Retrieve if logs are fake
// IN sensor Sensor number
// IN dateStart Date to start (write in Unix (s) format) . Time is not used
// IN dateEnd   Date to start (write in Unix (s) format) . Time is not used
// IN day : Define if we want a day view or a month view
// RET 1 if fake , 0 if not fake
function are_fake_logs ($sensor, $dateStart="", $dateEnd="", $day="day")
{

    // Open connection to dabase
    $db = \db_priv_pdo_start();


    if(strcmp("$dateStart","")==0) {
        $sql = "SELECT * FROM logs LIMIT 1";
    } else {
        // Get all point bewteen two dates
        if($day=="day") {
            $dateStartForLogsTable  = date ("ymd", $dateStart);
        } else {
            $dateStartForLogsTable  = date ("ym", $dateStart);
        }
        $sql = "SELECT timestamp , record1 , record2 FROM logs"
            . " WHERE sensor_nb = {$sensor}"
            . " AND timestamp LIKE  '{$dateStartForLogsTable}%'"
            . " AND fake_log != 'False' limit 1;";
    }


    try {
        $sth = $db->prepare($sql);
        $sth->execute();
        $row = $sth->fetch();
        if(strcmp("$dateStart","")==0) {
            if($row['fake_log']=="False") {
                    $row = null;
            }
        }
    } catch(\PDOException $e) {
        print_r($e->getMessage());
    }

    if ($row != null) 
    {
        return "1";
    } 
    else
    {
        return "0";
    }
}
// }}}


// {{{ reset_log()
// IN $table    table to be deleted: logs, power...
//    $start    delete logs between two specific dates, between $start and $end
//    $end      
// RET  0 is an error occured, 1 else
function reset_log($table="",$start="",$end="") {
    if($table == "") 
        return 0;
    
    $error=1;

    if($start == "" || $end == "") {
        $sql = "TRUNCATE TABLE {$table};";
    } else {
        $sql = "DELETE FROM {$table} WHERE date_catch BETWEEN '{$start}' AND '{$end}';";
    }
    
    $db=\db_priv_pdo_start("root");
    try {
        $db->exec($sql);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if((isset($ret))&&(!empty($ret))) {
          $error=0;
    }
    return $error;
}
// }}}



// {{{ reset_fake_log()
// IN 
// RET  0 is an error occured, 1 else
function reset_fake_log() {
    $sql = "DELETE FROM logs WHERE fake_log LIKE 'True';";

    $db=\db_priv_pdo_start("root");
    try {
        $db->exec($sql);
    } catch(\PDOException $e) {
        $ret=$e->getMessage();
    }
    $db=null;

    if((isset($ret))&&(!empty($ret))) {
          return $ret;
    }
    return 1;
}
// }}}


// {{{ save_log()
// IN  $file:   file to be saved
// IN  $month:  month of the file
// IN  $day:    day of the file
// IN  $type:   type of the file: logs or power
// RET  0 is an error occured, 1 else
function save_log($file="",$month=0,$day=0,$type="logs") {
    if(strcmp("$file","")==0) return 0;
    if($month==0) return 0;
    if($day==0) return 0;

    $path="";
    if(is_dir("tmp")) {
        $path="tmp";
    } else if(is_dir("../tmp")) {
        $path="../tmp";
    } else if(is_dir("../../tmp")) {
        $path="../../tmp";
    } else if(is_dir("../../../tmp")) {
        $path="../../../tmp";
    }   

    if(strcmp("$path","")==0) return 0;

    if(!is_dir("$path/saved_logs")) {
            if(!@mkdir("$path/saved_logs")) return 0;
    }

    if(!is_file("$file")) return 0;

    $name="$path/saved_logs/".date("Y")."_".$month."_".$day."_".$type."_loads_".date("Y")."_".date("m")."_".date("d")."_".date('His');
    if(!copy("$file","$name")) return 0;
    return 1;
}
// }}}

// {{{ addInMenu()
// ROLE Add in menu
// RET none
function addInMenu() {
    echo '<li id="menu-logs"><a class="href-logs" href="/bulcky/index.php?menu=logs"><span>Logs</span></a></li>';
}


}

?>
