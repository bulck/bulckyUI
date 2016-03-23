<?php 

    $ret = array();
    $err="";

    exec("dpkg -s bulckypi|grep Version|awk -F \"Version: \" '{print $2}'",$ret[0],$err);
    exec("dpkg -s bulckyface|grep Version|awk -F \"Version: \" '{print $2}'",$ret[1],$err);
    exec("dpkg -s bulckyraz|grep Version|awk -F \"Version: \" '{print $2}'",$ret[2],$err);
    exec("dpkg -s bulckytime|grep Version|awk -F \"Version: \" '{print $2}'",$ret[3],$err);
    exec("dpkg -s bulckyconf|grep Version|awk -F \"Version: \" '{print $2}'",$ret[4],$err);
    exec("dpkg -s bulckycam|grep Version|awk -F \"Version: \" '{print $2}'",$ret[5],$err);
    exec("dpkg -s bulckydoc|grep Version|awk -F \"Version: \" '{print $2}'",$ret[6],$err);
    if(is_file("/VERSION")) {
        exec("cat /VERSION",$ret[7],$err);
    } else {
        $ret[7]="000000";
    }

    echo json_encode($ret);
?>
