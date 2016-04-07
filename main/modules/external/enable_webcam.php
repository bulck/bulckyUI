<?php


$action=$_GET['action'];
if((!isset($_GET['webcam']))||(empty($_GET['webcam']))) {
    $webcam=0;
} else {
    $webcam=$_GET['webcam'];
}

exec("echo $webcam > /tmp/bulckycam_$action",$output,$err);
exec("sudo chown bulcky:bulcky /tmp/bulckycam_$action",$output,$err);
exec("sudo mv /tmp/bulckycam_$action /var/lock/",$output,$err);


if(isset($_GET['url'])) {
    require_once('../../libs/utilfunc.php');
    $url="";
    $webcam=$webcam+1;
    $port=get_reverse_webcam_port($webcam);
    if(strcmp("$port","")!=0) {
        exec("cat /etc/bulckyconf/reverse_ssh.conf|awk -F '=' '{print $2}'",$output,$err);
        if((count($output)==1)&&($output[0]!="")) {
            $url="http://".$output[0].":".$port."/?action=stream&v=".time();
        }
    }  else {
        $url="http://".$_SERVER['SERVER_ADDR'].":808".$webcam."/?action=stream&v=".time();
    }
}

$count=0;
while(true) {
    sleep(1);
	$count=$count+1;
    exec("ls /var/lock/bulckycam_${action} 2>/dev/null",$output,$err);

    if((strcmp("$err","0")!=0)||($count>=10)) break;
}

if(isset($_GET['url'])) {
    echo json_encode("$url");
}

?>
