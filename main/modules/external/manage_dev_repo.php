<?php

if((isset($_GET['beta']))&&(!empty($_GET['beta']))) {
    if(strcmp($_GET['beta'],"True")==0) {
        exec("echo 'deb http://www.greenbox-botanic.com/bulcky/repository-dev/armhf/ binary/' > /tmp/bulcky-dev.list",$output,$err);
        exec("sudo /bin/mv /tmp/bulcky-dev.list /etc/apt/sources.list.d/",$output,$err);
    } else {
        if(is_file("/etc/apt/sources.list.d/bulcky-dev.list")) {
            exec("sudo /bin/mv /etc/apt/sources.list.d/bulcky-dev.list /tmp/",$output,$err);
        }
    }
}

?>
