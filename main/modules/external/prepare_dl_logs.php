<?php 

    if((isset($_GET['action'])) && (!empty($_GET['action']))) {
        $action=$_GET['action'];
    } else {
        $action="";
    }
    
    $ret = array();
    $err="";
    $name="";
    
    switch ($action) {
        case "dl_logs_mysql" :
            exec("sudo cp /var/log/mysql/mysql.log /var/www/bulcky/tmp/export/ 2>/dev/null",$ret,$err);
            exec("sudo chown www-data:www-data /var/www/bulcky/tmp/export/mysql.log 2>/dev/null");
            if($err==0) {
                $name="mysql.log"; 
            }
            break;
        case "dl_logs_httpd" :
            exec("sudo cp /var/log/lighttpd/error.log /var/www/bulcky/tmp/export/lighttpd.log 2>/dev/null",$ret,$err);
            exec("sudo chown www-data:www-data /var/www/bulcky/tmp/export/lighttpd.log 2>/dev/null");
            if($err==0) {
                $name="lighttpd.log";
            }
            break;
        case "dl_logs_cultipi":
            exec("sudo cp /var/log/cultipi/cultipi.log /var/www/bulcky/tmp/export/ 2>/dev/null",$ret,$err);
            exec("sudo chown www-data:www-data /var/www/bulcky/tmp/export/cultipi.log 2>/dev/null");
            if($err==0) {
                $name="cultipi.log"; 
            }
            break;
        case "dl_logs_service":
            exec("sudo cp /var/log/cultipi/cultipi-service.log /var/www/bulcky/tmp/export/ 2>/dev/null",$ret,$err);
            exec("sudo chown www-data:www-data /var/www/bulcky/tmp/export/cultipi-service.log 2>/dev/null");
            if($err==0) {
                $name="cultipi-service.log";
            }
            break;
        case "dl_logs_system":
            exec("sudo cp /var/log/messages /var/www/bulcky/tmp/export/ 2>/dev/null",$ret,$err);
            exec("sudo chown www-data:www-data /var/www/bulcky/tmp/export/messages 2>/dev/null");
            if($err==0) {
                $name="messages";
            }
            break;
        default:
            break;
    }
    
    echo json_encode($name);
 
?>
