<?php

require_once('../../libs/config.php');

if((isset($_GET['page']))&&(!empty($_GET['page']))) {
   $page=$_GET['page'];

    require_once('../../libs/config.php');
    require_once('../../libs/db_get_common.php');
    require_once('../../libs/db_set_common.php');
    require_once('../../libs/debug.php');
    require_once '../../libs/utilfunc.php';
    require_once('../../libs/utilfunc_sd_card.php');

    if((isset($_GET['get_array']))&&(!empty($_GET['get_array']))) {
        $get_array=json_decode($_GET['get_array'],true);
        foreach(array_keys($get_array) as $get) {
            ${$get}=$get_array[$get];

            if($GLOBALS['DEBUG_TRACE']) {
                echo $get."-----".$get_array[$get]."<br />";
            }
        }
    }



    if(strpos($_SERVER['REMOTE_ADDR'],"10.0.0.100")!==false) {
        if((!isset($_COOKIE['ADHOC']))||(strcmp($_COOKIE['ADHOC'],"True")!=0)) {
            $page="configuration";
            $submenu="network_conf_ui";
            setcookie("ADHOC", "True", time()+(86400 * 30),"/",false,false);
        }
    }


    ob_start();
    
    // Check if it's a plugin or not
    if (!in_array($page, $GLOBALS['PLUGIN']))
    {
        $page="welcome";
    }


    // Load lib
    $fileName = '../../plugin/' . $page . '/lib_' . $page . '.php';
    if (is_file($fileName)) {
        require_once $fileName;
    }
    
    // Load script
    $fileName = '../../plugin/' . $page . '/script_' . $page . '.php';
    if (is_file($fileName)) {
        include $fileName;
    }

    include '../../libs/post_script.php';
         
    // Load javascript
    $fileName = '../../plugin/' . $page . '/' . $page . '.js';
    if (is_file($fileName)) {
        include $fileName;
    }
        
    // Load HTML
    $fileName = '../../plugin/' . $page . '/' . $page . '.html';
    if (is_file($fileName)) {
        include $fileName;
    }
        
    include '../../libs/js/send_info_error.js';
        

    $include = ob_get_clean();
    echo $include;
}
?>
