<?php

require_once('../../../main/libs/config.php');
require_once('../../../main/libs/db_get_common.php');
require_once('../../../main/libs/db_set_common.php');
require_once('../../../main/libs/utilfunc.php');
require_once('../../../main/libs/utilfunc_sd_card.php');

$title=$_GET["title"];
$start=$_GET["start"];
$end=$_GET["end"];
$color=$_GET["color"];
$important=$_GET["important"];
$main_error=array();


if(    isset($title) && !empty($title)
    && isset($start) && !empty($start)
    && isset($end)   && !empty($end)
    && isset($color) && !empty($color)) {
    
        if($db = db_priv_pdo_start()) {
        
            if((isset($_GET["desc"]))&&(!empty($_GET["desc"]))) {
                $description=$db->quote($_GET["desc"]);    
            } else {
                // Waring '' are very important !
                $description = "''";
            }


            $sql = "INSERT INTO calendar (Title, StartTime, EndTime, Description, Color, Important)"
                . " VALUES ({$db->quote($title)}, '{$start}', '{$end}', {$description}, '{$color}', '${important}');";

            try {
                $db->exec($sql);
            } catch(PDOException $e) {
                print_r($e->getMessage());
            }

            $db=null;
        }

}
echo "1";
?>
