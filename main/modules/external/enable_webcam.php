<?php

$action=$_GET['action'];
$webcam=$_GET['webcam'];
exec("echo $webcam > /var/lock/culticam_$action",$output,$err); 
exec("sudo chown cultipi var/lock/culticam_$action",$output,$err);
sleep(5);
?>
