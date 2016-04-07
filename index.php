<?php

// Don't put pages in cache:
session_cache_limiter('nocache');
$error=array();

$lang="fr_FR";
setcookie("LANG", "fr_FR", time()+(86400 * 30),"/",false,false);

//Cookie permettant de stocker le positionnement de la boîte de message, valable pendant 30 jours
if (!isset($_COOKIE['POSITION'])) {
    setcookie("POSITION", "15,15,325", time()+(86400 * 30),"/",false,false);
}

//Timezone par défaut pour éviter les problèmes d'ajout d'heures lors des transformations des temps
date_default_timezone_set('UTC');

//Minimal library required:
require_once('main/libs/config.php');
require_once('main/libs/db_get_common.php');
require_once 'main/libs/utilfunc.php';

// Load every plugins
foreach ($GLOBALS['PLUGIN'] as $plugin) {
    $fileName = 'main/plugin/' . $plugin . '/lib_' . $plugin . '.php';
    if (is_file($fileName))
    {
        require_once $fileName;
    }
}

__('LANG');


setcookie("CHECK_SD", "False", time()+1800,"/",false,false);

// Library required:
require_once('main/libs/db_set_common.php');
require_once('main/libs/debug.php');
require_once('main/libs/utilfunc_sd_card.php');

// Check database consistency
check_database();

?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>Bulcky</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<link rel="icon" href="favicon.ico" />
		
		<!--[if lte IE 8]><script src="css/ie/html5shiv.js"></script><![endif]-->
		<script src="js/jquery.min.js?v=<?=@filemtime('js/jquery.min.js')?>"></script>
		<script src="js/jquery-ui.js?v=<?=@filemtime('js/jquery-ui.js')?>"></script>
		<script src="js/skel.min.js?v=<?=@filemtime('js/skel.min.js')?>"></script>
		<script src="js/skel-layers.min.js?v=<?=@filemtime('js/skel-layers.min.js')?>"></script>
		<script src="js/init.js?v=<?=@filemtime('js/init.js')?>"></script>
        <script src="js/bulcky.js?v=<?=@filemtime('js/bulcky.js')?>"></script>
        <script src="js/bulcky-utils.js?v=<?=@filemtime('js/bulcky-utils.js')?>"></script>
        <script src="js/jquery.blockUI.js?v=<?=@filemtime('js/jquery.blockUI.js')?>"></script>
        <script src="js/fileUpload.js?v=<?=@filemtime('js/fileUpload.js')?>"></script> 
        <script src="js/highcharts.js?v=<?=@filemtime('js/highcharts.js')?>"></script>
        <script src="js/exporting.js?v=<?=@filemtime('js/exporting.js')?>"></script>
        <script src="js/jquery-ui-timepicker-addon.js?v=<?=@filemtime('js/jquery-ui-timepicker-addon.js')?>"></script>
        <script src="js/jquery.colourPicker.js?v=<?=@filemtime('js/jquery.colourPicker.js')?>"></script>
        <script src="js/fullcalendar.js?v=<?=@filemtime('main/libs/js/fullcalendar.js')?>"></script>
        <script src="js/fileDownload.js?v=<?=@filemtime('main/libs/js/fileDownload.js')?>"></script>
        <script src="js/jquery.ui.datepicker-fr.js?v=<?=@filemtime('js/jquery.ui.datepicker-fr.js')?>"></script>
   
    
    
   
		
		<link rel="stylesheet" href="css/skel.css?v=<?=@filemtime('css/skel.css')?>" />
        <link rel="stylesheet" href="css/jquery-ui.custom.css?v=<?=@filemtime('css/jquery-ui.custom.css')?>" />
		<link rel="stylesheet" href="css/style.css?v=<?=@filemtime('css/style.css')?>" />
        <link rel="stylesheet" href="css/bulcky.css?v=<?=@filemtime('css/bulcky.css')?>" />
		<!--[if lte IE 8]><link rel="stylesheet" href="css/ie/v8.css" /><![endif]-->


	</head>
	<body>
    <div id="diff_conf" style="display:none">
        <?php echo __('DIR_CONF_NOT_UPTODATE_POPUP'); ?>
        <p class="disable_popup">
            <?php echo __('DISABLE_POPUP'); ?>
            <input type="checkbox" id="disable_popup" name="disable_popup" />
        </p>
    </div>

    <div id="sync_conf" style="display:none">
        <?php echo __('SYNC_CONF'); ?>
    </div>

    <div id="diff_conf_list" title="<?php echo __('DIFF_CONF'); ?>" style="display:none">
    </div>

    <div id="tooltip_msg_box" style="display:none" class="eyes_msgbox"><i class="fa fa-lg fa-eye" id="eyes_msgbox"></i></div>

    <!-- Small eye for displaying message pop up-->
    <script>title_msgbox="<?php echo __('TOOLTIP_MSGBOX_EYES'); ?>";</script>

	<div id="elevator-up" class="elevator-up">
		<a href="" id="arrow_up" title="Haut de page" class="icon-elevator fa-arrow-up" style="visibility:hidden"></a>
	</div>

	<div id="elevator-down" class="elevator-down">
		<a href="" style="visibility:hidden" title="Bas de page" id="arrow_down" class="icon-elevator fa-arrow-down"></a>
	</div>

	<img src="./images/logo_bulck.png" class="logo" alt="" />

	<!-- Header -->
		<div id="header">
			<!-- Menu -->
				<nav id="nav">
					<ul id="menubar-ul">
                        <?php
                            // If there are some plugins to show in menu, display it
                            foreach ($GLOBALS['PLUGIN'] as $plugin) {
                                // Check if function exists
                                if (function_exists($plugin . '\addInMenu'))
                                {
                                    call_user_func($plugin . '\addInMenu');
                                }
                            }
                        ?>
					</ul>
				</nav>
		</div>

        <div class="message" style="display:none" title="<?php echo __('MESSAGE_BOX'); ?>">
            <br />
            <div id="pop_up_information_container">
                <span class="info_title">
                <i class="fa fa-info-circle fa-2x icon_info"></i>
                <b><?php echo __('INFORMATION'); ?>:</b></span>
                <br /><br />
                <div class="info"  id="pop_up_information_part">
                    <ul>
                    </ul>
                    <br />
                </div>
            </div>

            <div id="pop_up_error_container">
                <span class="error_title"><i class="fa fa-2x fa-exclamation-triangle icon_error"></i>
                <b><?php  echo __('WARNING'); ?>:</b></span>
                <div class="error" id="pop_up_error_part">
                    <ul>
                    </ul>
                </div>
            </div>
        </div>

		<!-- Banner -->
			<div id="banner">
			    <a href="/bulcky/index.php?menu=welcome" class="href-welcome"><img src="./images/bulck.png" class="logo_bulck" alt="" /></a>
			</div>
		
		<nav class="nav"></nav>
		<!-- Highlights -->
			<div class="wrapper style1">
				<!-- Div content -->
				<div class="container" id="content">
                     <!-- To check that javascript is enable: -->
                    <noscript>
                        <div id="compat-js" class="compat">
                            <?php echo __('ENABLE_JAVASCRIPT'); ?>
                        </div>
                    </noscript>
				</div>
			</div>
		
		
			<nav class="nav"></nav>
			
			<!-- Footer -->
			<div id="footer">
                <br />
				<!-- Copyright -->
					<div class="copyright">
						<ul class="menu">
                            <li>Contact : <a href="mailto:info@bulck.fr">info@bulck.fr</a></li>
							<li>&copy; Bulck SAS. Tout droits réservés</li>
						</ul>
					</div>

			</div>
	</body>
</html>
