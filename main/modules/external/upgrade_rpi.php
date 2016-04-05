<?php 

$err="";
$output=array();
exec("sudo apt-get install -y  --force-yes --only-upgrade -o Dpkg::Options::=\"--force-confdef\" -o Dpkg::Options::=\"--force-confold\" bulckyface bulckytime bulckyraz bulckyconf bulckypi bulckydoc bulckycam",$output,$err);


?>
