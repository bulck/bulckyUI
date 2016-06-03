<?php 

// Affiche l'utilisation d'une prise
// vi /var/www/cultibox/main/modules/external/get_plugUse.php
// chmod 777 /var/www/cultibox/main/modules/external/get_plugUse.php
// 88.185.152.80:33000/cultibox/main/modules/external/get_plugUse.php?plug=7&year=16&month=04

require_once('../../libs/utilfunc.php');
require_once('../../libs/utilfunc_sd_card.php');
require_once('../../libs/db_get_common.php');
require_once('../../libs/db_set_common.php');
require_once('../../libs/config.php');

$error=array();


//Récupération du nom de la variable, par convention interne, les noms de COOKIE  sont 
//toujours en majuscule, on capitalise donc le nom récupéré:
$plug = "";
if((isset($_GET['plug']))&&(!empty($_GET['plug']))) {
    $plug=$_GET['plug'];
}
$year = "";
if((isset($_GET['year']))&&(!empty($_GET['year']))) {
    $year=$_GET['year'];
}
$month = "";
if((isset($_GET['month']))&&(!empty($_GET['month']))) {
    $month=$_GET['month'];
}

if($plug != "" && $year != "" && $month != "") {
    $db = \db_priv_pdo_start();
    
    $sql = "select SUBSTR(timestamp,5,2), SUBSTR(timestamp,9,2), SUBSTR(timestamp,11,2) , SUBSTR(timestamp,13,2) , record FROM power WHERE plug_number = '{$plug}'"
            . " AND timestamp LIKE '{$year}{$month}%' ORDER BY timestamp;";

    try {
        $sth = $db->prepare($sql);
        $sth->execute();
    } catch(\PDOException $e) {
        print_r($e->getMessage());
    }

    $day = 0;
    $nbSec = array();
    while ($row = $sth->fetch()) 
    {
        if ($row["SUBSTR(timestamp,5,2)"] != $day) {
            $startSec = 0;
            $day = $row["SUBSTR(timestamp,5,2)"];
            $nbSec[$day] = 0;
        }
        $secActual = $row["SUBSTR(timestamp,9,2)"] * 3600 + $row["SUBSTR(timestamp,11,2)"] * 60 + $row["SUBSTR(timestamp,13,2)"];
        if ($row["record"] == "9990" && $startSec == 0) {
            $startSec = $secActual;
        } elseif ($row["record"] == "0" && $startSec != 0) {
            $nbSec[$day] = $nbSec[$day] + $secActual - $startSec;
            $startSec = 0;
        }
    }

    echo json_encode($nbSec);
    break;
    
} else {
    echo 'param not defined';
    
}



?>
