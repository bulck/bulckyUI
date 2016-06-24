<?php

    require_once('../../../main/libs/config.php');
    require_once('../../../main/libs/db_get_common.php');
    require_once('../../../main/libs/db_set_common.php');
    require_once('../../../main/libs/utilfunc.php');
    require_once('../../../main/libs/utilfunc_sd_card.php');


    $id=$_GET["id"];

    $sd_card=$_GET["card"];
    
    $main_error=array();
    
    // Init connexion to DB
    $db = db_priv_pdo_start("root");

    // Read days to update
    $sql = "SELECT * FROM calendar WHERE Id = '{$id}' ;";
    try {
        $sth = $db->prepare($sql);
        $sth->execute();
        $res = $sth->fetch();
    } catch(\PDOException $e) {
        print_r($e->getMessage());
    }
    
    $start = $res['StartTime'];
    $end = $res['EndTime'];

    // Delete event from calendar
    $sql = "DELETE FROM calendar WHERE Id = '{$id}' ;";

    $sth=$db->prepare($sql);
    $sth-> execute();
    $res=$sth->fetchAll(PDO::FETCH_ASSOC);
    $db=null;

?>
