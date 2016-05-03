<?php 

    if((isset($_GET['action'])) && (!empty($_GET['action']))) {
        $action=$_GET['action'];
    }
    
    $ret = array();
    $ret_var="";
    $err="";
    
    switch ($action) {
        case "restart_bulckypi" :
            exec("sudo /etc/init.d/bulckypi force-reload >/dev/null 2>&1",$ret,$ret_var);
            break;
        case "status_bulckypi" :
            exec("/etc/init.d/bulckypi status >/dev/null 2>&1",$ret,$ret_var);
            break;
        case "restart_rpi" :
            exec("sudo /sbin/shutdown -r now >/dev/null 2>&1",$ret,$ret_var);
            break;
        default:
            break;
    }
    
    echo json_encode($ret_var);
 
?>
