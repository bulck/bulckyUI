<?php

$err=1;
if((isset($_GET['action']))&&(!empty($_GET['action']))) {
    $action=$_GET['action'];

    switch($action) {
        case 'change_password' :
        case 'add_user' :
            if((isset($_GET['pwd']))&&(!empty($_GET['pwd']))&&(isset($_GET['login']))&&(!empty($_GET['login']))) {
                $err=0;
                exec("(echo -n '".$_GET['login'].":Identification:' && echo -n '".$_GET['login'].":Identification:".$_GET['pwd']."' | md5sum | awk '{print $1}')",$output,$err);
                $new_pwd=$output;
                if($err==0) {
                    exec("cp /etc/lighttpd/.passwd /tmp/",$output,$err);
                    if(is_file("/tmp/.passwd")) {
                        if(strcmp("$action","change_password")==0) {
                            exec("sed -i 's#".$_GET['login'].".*#".$output[0]."#g' /tmp/.passwd ",$output,$err);
                         } else {
                             exec("echo '".$output[0]."' >> /tmp/.passwd ",$output,$err);
                         }
                        exec("sudo mv /tmp/.passwd /etc/lighttpd/",$output,$err);
                    } else {
                        $err=1;
                    }
                } 
                
                if($err==0) {
                    $output="1";
                } else {
                    $output="0";
                } 
            } else {
                $output="0";
            }
        break;

        case 'get_login' :
            exec("cat /etc/lighttpd/.passwd|awk -F \":\" '{print $1}'",$output,$err);
        break;

        case 'delete_user' :
             if((isset($_GET['login']))&&(!empty($_GET['login']))) {
                $login=$_GET['login'];
                $err=0;
                exec("cp /etc/lighttpd/.passwd /tmp/",$output,$err);
                if(is_file("/tmp/.passwd")) {
                    exec("sed -i s/$login:Identification:.*//g /tmp/.passwd",$output,$err);
                    exec("sudo mv /tmp/.passwd /etc/lighttpd/",$output,$err);
                } else {
                    $err=1;
                }

                 if($err==0) {
                    $output="1";
                } else {
                    $output="0";
                }
             } else {
                $output="0";
            }
        break;
    }
}
        

if($err==1) {
    echo json_encode("0");
} else {
    echo json_encode($output);
}

?>
