<?php

// Load libraries
if(is_file("main/libs/l10n.php")) {
   require_once 'main/libs/l10n.php';
   require_once 'main/libs/lib_sensors.php';
   require_once 'main/libs/lib_power.php';
   require_once 'main/plugin/bulcky/lib_bulcky.php';
   $PATH_PLUGIN='main/plugin';

} else if(is_file("../libs/l10n.php")) {
   require_once '../libs/l10n.php';
   $PATH_PLUGIN='../plugin';
   require_once '../libs/lib_sensors.php';
   require_once '../libs/lib_power.php';
   require_once '../lib_bulcky.php';
} else if(is_file("../../libs/l10n.php")) {
   require_once '../../libs/l10n.php';
   $PATH_PLUGIN='../../plugin';
   require_once '../../libs/lib_sensors.php';
   require_once '../../libs/lib_power.php';
   require_once '../../plugin/bulcky/lib_bulcky.php';
} else {
   require_once '../../../libs/l10n.php';
   $PATH_PLUGIN='../../../plugin';
   require_once '../../../libs/lib_sensors.php';
   require_once '../../../libs/lib_power.php';
   require_once '../../../plugin/bulcky/lib_bulcky.php';
}


$handler = opendir($PATH_PLUGIN);
while ($file = readdir($handler)) {
    $handler_plugin = opendir($PATH_PLUGIN."/".$file);
    while ($plugin = readdir($handler_plugin)) {
        if ($plugin != "." && $plugin != "..") {
            if(strpos($plugin, 'lib_') !== false) {
                require_once  "$PATH_PLUGIN/".$file."/".$plugin;
            }
        }
    }
}
    



// {{{ __($msgkey, ...)
// ROLE get the translated message corresponding to $msgkey, the parameters may
// be used in $msgkey by using the sprintf syntax (%s)
// IN $msgkey
// RET string translated
static $__translations;
static $__translations_fallback;
static $string_lang;

function __() {
   global $__translations;
   global $__translations_fallback;
   global $string_lang;
   
   if (func_num_args() < 1) {
      die("ERROR: __() called without arguments");
      return "";
   }
   $args = func_get_args();

  
   if (!isset($__translations)) {
      $__translations = __translations_get("fr_FR");
      $__translations_fallback = __translations_get(LANG_FALLBACK);

      if (empty($__translations_fallback)) {
         die("No translation file");
      }
      
      $string_lang = array($_COOKIE['LANG'] => $__translations);
   }
   
   $msg = $args[0];
   if (isset($__translations["$msg"])) {
      $msg = $__translations["$msg"];
   } elseif (isset($__translations_fallback["$msg"])) {
      $msg = $__translations_fallback["$msg"];
   } else {
      die("WARNING:L10N: no translation for '$msg'");
   }
   
   $args[0] = $msg;
   $ret = call_user_func_array('sprintf', $args);


   if(isset($args[1])) {
        return $ret;
   } else {
        return htmlentitiesOutsideHTMLTags($ret);
   }
}
//}}}


// {{{ htmlentitiesOutsideHTMLTags()
// ROLE encode a string in HTML and preserve HTML tags
// IN  $htmltext        text to encode
// RET text encoded in HTML
function htmlentitiesOutsideHTMLTags($htmlText)
{
    $matches = Array();
    $sep = '###HTMLTAG###';

    preg_match_all(":</{0,1}[a-z]+[^>]*>:i", $htmlText, $matches);

    $tmp = preg_replace(":</{0,1}[a-z]+[^>]*>:i", $sep, $htmlText);
    $tmp = explode($sep, $tmp);

    for ($i=0; $i<count($tmp); $i++) $tmp[$i] = htmlentities($tmp[$i]);
    $tmp = join($sep, $tmp);
    for ($i=0; $i<count($matches[0]); $i++) $tmp = preg_replace(":$sep:", $matches[0][$i], $tmp, 1);

    return $tmp;
}
// }}}


// {{{ getvar()
// ROLE Used to get access to GET or POST values for '$varname'
// IN $varname as a string
// RET the string value as read in $_GET[] or $_POST[]
function getvar($varname) {
    $tmp = false;

    // The mess with the *x appended is a workaround for some browser
    // versions that append it when you want to get information for some
    // image buttons

    if (isset($_GET["$varname"])) {
        $tmp = $_GET["$varname"];
    } elseif (isset($_GET["$varname"."x"])) {
        $tmp = true;
    } elseif (isset($_GET["$varname"."_x"])) {
        $tmp = true;
    } elseif (isset($_POST["$varname"])) {
        $tmp = $_POST["$varname"];
    } elseif (isset($_POST["$varname"."x"])) {
        $tmp = true;
    } elseif (isset($_POST["$varname"."_x"])) {
        $tmp = true;
    }

    if (is_array($tmp)) {
        return $tmp;
    } else {
        return stripslashes(htmlentities($tmp));
    }
}
// }}}


// {{{ check_empty_string()
// ROLE check is a string is empty or only composed with invisible caracters
// IN $value         string to check
// RET false if the string is empty, true else
function check_empty_string($value="") {
   if(strcmp(trim($value),"")==0) {
      return 0;
   } else {
      return 1;
   }
}
//}}}


// {{{ check_format_date()
// ROLE check date format (month with the format MM ou complete date: YYYY-MM-DD)
// IN $date       date to check
//    $type       the type: month or days
// RET true is the format match the type, false else
function check_format_date($date="",$type) {
   $date=str_replace(' ','',"$date");
   if("$type"=="days") {
      if(strlen("$date")!=10) {
         return 0;
      }

      $tmp=explode("-","$date");
      if(count($tmp)!=3) return 0;

      if(!preg_match('#^[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9]$#', $date)) {
         return 0;
      }

      return checkdate($tmp[1], $tmp[2], $tmp[0]);
   }

   if("$type" == "month") {
      if(strlen("$date")!=7) {
         return 0;
      }

      $tmp=explode("-","$date");
      if(count($tmp)!=2) return 0;

      if(!preg_match('#^[0-9][0-9][0-9][0-9]-[0-9][0-9]$#', $date)) {
         return 0;
      }

      return checkdate($tmp[1], "1", $tmp[0]);
   }
   return 0;
}
//}}}


// {{{ check_numeric_value()
// ROLE check if a value is numeric or not
// IN $value         value to check
// RET true is $value is numeric, false else
function check_numeric_value($value="") {
   if("$value"=="0") {
         return true;
   }

   if((empty($value))||(!isset($value))) {
      return false;
   }

    $value=str_replace(",",".",$value);

   if(!is_numeric($value)) {
      return false;
   }
   return true;
}
//}}}


// {{{ check_times()
// ROLE check times send by user to reccord a plug behaviour
// IN   $start_time      time starting the event
//      $end_time        time ending the event
// RET 1 if ok, 0 if there is an error or 2 if start time > end time
function check_times($start_time="",$end_time="") {
   if((!empty($start_time))&&(isset($start_time))&&(!empty($end_time))&&(isset($end_time))) {
      $start_time=str_replace(' ','',"$start_time");

      if(strcmp($start_time,$end_time)==0) {
         return 0;
      }

      $end_time=str_replace(' ','',"$end_time");

      $sth=substr($start_time, 0, 2);
      $stm=substr($start_time, 3, 2);
      $sts=substr($start_time, 6, 2);

      $enh=substr($end_time, 0, 2);
      $enm=substr($end_time, 3, 2);
      $ens=substr($end_time, 6, 2);
   
      $start_time= mktime($sth, $stm, $sts);
      $end_time= mktime($enh, $enm, $ens);

      if($start_time >= $end_time) {
              return 2;
      }   

      return 1;         
   }
   return 0;
}
// }}}



// {{{ format_program_highchart_data()
// ROLE format data to be used by highchart for the programs part
// IN   $arr               an array containing datas
//      $date_start          
// RET data for highchart and cultibox programs
function format_program_highchart_data($arr,$date_start="") {
    $data="";
    if(empty($date_start)) {
        $ref_day=1;
        $ref_month=1;
        $ref_year=1970;
    } else {
        $ref_year=substr($date_start, 0, 4);
        $ref_month=substr($date_start, 5, 2);
        $ref_day=substr($date_start, 8, 2);
    }
   date_default_timezone_set('UTC');
   if($GLOBALS['DEBUG_TRACE']) {
         echo "<br />Debug Trace Highchart Data:<br />";
   }
   if(count($arr)>0) {
      if(is_array($arr)) {
      foreach($arr as $value) {
         if((empty($data))&&(strcmp($value['time_start'],"000000")!=0)) {
            $first=mktime(0,0,0,$ref_month,$ref_day,$ref_year)*1000;
            $data="[".$first.",0]";
            $last_time=$first;
            $last_value="0";
         } else if((empty($data))&&(strcmp($value['time_start'],"000000")==0)) {
            $val_start=mktime(0,0,0,$ref_month,$ref_day,$ref_year)*1000;
            $ehh=substr($value['time_stop'],0,2);
            $emm=substr($value['time_stop'],2,2);
            $ess=substr($value['time_stop'],4,2);
            $val_end=mktime($ehh,$emm,$ess,$ref_month,$ref_day,$ref_year)*1000;
            if(strcmp($value['time_stop'],"235959")==0) {
                                   $data="[".$val_start.",".$value['value']."],[".$val_end.",".$value['value']."]";
            } else {
               $data="[".$val_start.",".$value['value']."],[".$val_end.",".$value['value']."]";
               $last_time=$val_end;
               $last_value=$value['value'];
            }
         }


         if((!empty($data))&&($value['value']!=0)&&(strcmp($value['time_start'],"000000")!=0)) {
            $shh=substr($value['time_start'],0,2);
            $smm=substr($value['time_start'],2,2);
            $sss=substr($value['time_start'],4,2);
            $val_start=mktime($shh,$smm,$sss,$ref_month,$ref_day,$ref_year)*1000;
            $ehh=substr($value['time_stop'],0,2);
            $emm=substr($value['time_stop'],2,2);
            $ess=substr($value['time_stop'],4,2);
            $val_end=mktime($ehh,$emm,$ess,$ref_month,$ref_day,$ref_year)*1000;
               if(strcmp($value['time_stop'],"235959")!=0) {
                  if("$last_time"!="$val_start") {
                     $data=$data.",[".$last_time.",0],[".$val_start.",0],[".$val_start.",".$value['value']."],[".$val_end.",".$value['value']."]";
                  } else {
                     $data=$data.",[".$val_start.",".$value['value']."],[".$val_end.",".$value['value']."]";
                  }
                  $last_time=$val_end;
                  $last_value=$value['value'];
               } else {
                   if("$last_time"!="$val_start") {
                        $data=$data.",[".$last_time.",0],[".$val_start.",0],[".$val_start.",".$value['value']."],[".$val_end.",".$value['value']."]";
                   } else {
                        $data=$data.",[".$val_start.",".$value['value']."],[".$val_end.",".$value['value']."]";
                   }
               }
         }
         $last_val=$value;
      }
  }
      if((!empty($data))&&(strcmp($last_val['time_stop'],"235959")!=0)) {
         if("$ref_year"=="1970") {
            $last=mktime(0,0,0,$ref_month,$ref_day+1,$ref_year)*1000;
         } else {
            $last=mktime(23,59,59,$ref_month,$ref_day,$ref_year)*1000; 
         }
         $data=$data.",[".$last_time=$val_end.",0],[".$last.",0]";
      } 
   } else {
      $first=mktime(0,0,0,$ref_month,$ref_day,$ref_year)*1000;
      $last=mktime(0,0,0,$ref_month,$ref_day+1,$ref_year)*1000;
      $data="[".$first.",0],[".$last.",0]";
   }
   return $data;
}
// }}}


// {{{ check_date()
// ROLE check the an interval of dates is correct
// IN   $datestart      first date
//      $dateend        second date
// RET false if $datestart => $dateend, true else
function check_date($datestart="",$dateend="") {
     $year_start=substr($datestart,0,4); 
     $month_start=substr($datestart,5,2);
     $day_start=substr($datestart,8,2);

     $year_end=substr($dateend,0,4);
     $month_end=substr($dateend,5,2);
     $day_end=substr($dateend,8,2);

     if($year_start<$year_end) {
           return true;
     }

     if($year_start>$year_end) {
           return false;
     } 

     if($month_start<=$month_end) {
          if($month_start==$month_end) {
              if($day_start<=$day_end) {
                  return true;
              }
          } else {
              return true;
          }
     }
     return false;
}
// }}}


// {{{ check_tolerance_value()
// ROLE check the tolerance value
// IN $type               the type of the plug 
//    $tolerance          the value to check
// RET false is there is a wrong value, true else
function check_tolerance_value($type,&$tolerance=0) {
   $tolerance = str_replace(",",".",$tolerance);

    if(!is_numeric($tolerance)) return false;

    switch ($type) {
        case "extractor":
        case "intractor":
        case "ventilator":
        case "heating":
        case "pumpfilling":
        case "pumpempting":
        case "pump":
            if($tolerance >= 0 && $tolerance <= 10) {
                return true;
            } else {
                return false;
            }
            break;
        case "humidifier":
        case "dehumidifier":
            if($tolerance >= 0 && $tolerance <= 25.5) {
                return true;
            } else {
                return false;
            }
            break;
        case "electrovanne_co2":
            if($tolerance >= 0 && $tolerance <= 1000) {
                return true;
            } else {
                return false;
            }
            break;            
        default:
            return true;
            break;
    }
}
// }}}


//HERE
// {{{ check_format_values_program()
// ROLE check AND format value of a program 
// IN   $value       value to check and format
// IN   $type        temp or humi - type to check
// IN   $tol         tolerance if the plug
// RET error array containing, error message, min and max depending tolerance
function check_format_values_program($value="0",$type="temp",$tol=0) {
   $value=str_replace(',','.',$value);
   $value=str_replace(' ','',$value);

   $ret=array();
   $ret['error']=1;
   $ret['min']="";
   $ret['max']="";
   $ret['unity']="";

   $check=true;

   if(!is_numeric($value)) {   
       $ret['error']=2;
   } else {
       if((($value+$tol)>$GLOBALS['LIMIT_PLUG_PROGRAM'][$type]['max'])||
          (($value-$tol)<$GLOBALS['LIMIT_PLUG_PROGRAM'][$type]['min'])) {
            switch($type) {
                case 'temp':  
                    $ret['error']=3;
                    $ret['unity']="°C";
                    break;

                case 'humi':  
                    $ret['error']=4;
                    $ret['unity']="%";
                    break;

                case 'cm':  
                    $ret['error']=5;
                    $ret['unity']="cm";
                    break;

                case 'ppm':  
                    $ret['error']=6;
                    $ret['unity']="ppm";
                    break;

                case 'other':  
                    $ret['error']=6;
                    break;
                case 'purcent':
                    $ret['error']=7;
                    $ret['unity']="%";
                    break;
            }
            $ret['min']=$GLOBALS['LIMIT_PLUG_PROGRAM'][$type]['min']+$tol;
            $ret['max']=$GLOBALS['LIMIT_PLUG_PROGRAM'][$type]['max']-$tol;
       } 
   }

   return $ret;
}
// }}}


// {{{ check_regul_value()
// ROLE check is a value for the regulation is correct
// IN   $value       value to check
// OUT  false is there is a wrong value, true else
function check_regul_value($value="0") {
   $value=str_replace(',','.',$value);
   $value=str_replace(' ','',$value);
   if(($value>99.99)||($value<0)) return false;
   if(!is_numeric($value)) return false;
   return true;
}
// }}}


// {{{ clean_popup_message()
// ROLE clean highchart message by removing non-appropriate char for javascript
// IN  $message         message to be cleaned
// RET   new message cleaned 
function clean_highchart_message($message="") {
   $old = array("'","&eacute;","&agrave;","&egrave;","&ecirc;","&deg;","&ucirc;","&ocirc;");
   $new   = array("\'","é","à","è","ê","°","û","ô");
   return str_replace($old, $new, $message)."\\n\\n";
}
// }}}


// {{{ clean_calendar_message()
// ROLE clean calendar field to be formated for the cultibox that doesn't manage accents
// IN  $message         message to be cleaned
// RET new message cleaned 
function clean_calendar_message($message="") {
    $search = array('À','Á','Â','Ã','Ä','Å','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ò','Ó','Ô','Õ','Ö','Ù','Ú','Û','Ü','Ý','à','á','â','ã','ä','å','ç','è','é','ê','ë','ì','í','î','ï','ð','ò','ó','ô','õ','ö','ù','ú','û','ü','ý','ÿ',"\n");
    $replace = array('A','A','A','A','A','A','C','E','E','E','E','I','I','I','I','O','O','O','O','O','U','U','U','U','Y','a','a','a','a','a','a','c','e','e','e','e','i','i','i','i','o','o','o','o','o','o','u','u','u','u','y','y',"\r\n");
    $res=str_replace($search, $replace, $message);

    $tmp="";
    for($i=0;$i<strlen($res);$i++) { 
        if(((ord($res[$i])>=32)&&(ord($res[$i])<=122))||(ord($res[$i])==10)||(ord($res[$i])==13)) {
            $tmp=$tmp.$res[$i];
        } else {
            $tmp=$tmp." ";
        }
    }
    return $tmp;
}
// }}}


// {{{ popup_message()
// ROLE popup message formatting
// IN  $message         message to be formatted
// RET new message cleaned 
function popup_message($message="") {
   return $message."<br /><br />";
}
// }}}


// {{{ get_nb_days()
// ROLE get number of days beetwen two dates
// IN   $start_date       first date
//      $end_date         second date
// RET   return number of days beetwen dates or -1
function get_nb_days($start_date="",$end_date="") {
         if((!isset($start_date))||(empty($start_date))||(!isset($end_date))||(empty($end_date))) { return -1; }

          $year_start=substr($start_date,0,4); 
          $month_start=substr($start_date,5,2);
          $day_start=substr($start_date,8,2);
          $year_end=substr($end_date,0,4);
          $month_end=substr($end_date,5,2);
          $day_end=substr($end_date,8,2);

          $first=mktime(0,0,0,$month_start,$day_start,$year_start);
          $second=mktime(0,0,0,$month_end,$day_end,$year_end);

          if($first>$second) { return -1; }

          return round(($second-$first)/86400);
}
// }}}


// {{{ format_data_sumary()
// ROLE format actions of a plug to be displayed in a sumary
// IN    $data       plug's informations array
// RET   sumary formated 
function format_data_sumary($data) {
    $resume=array();
    $unity="";
    $number=1;
    foreach($data as $plugs_info) {
        $resume[$number]="";
        if((empty($plugs_info["data"]))||(empty($plugs_info['PLUG_NAME']))||(empty($number))|(empty($plugs_info['PLUG_TYPE']))) {
            $resume[]="<p align='center'><b><i>".__('SUMARY_TITLE')." ".$number.":<br /> ".$plugs_info['PLUG_NAME']."</i></b></p><p align='center'>".__('EMPTY_ACTION')."</p>";
        } else {
            if(strcmp($plugs_info['PLUG_MODULE'],"pwm")==0) {
              $unity="%";
              $val_on=__('SUMARY_VARIO_ON');
              $val_off=__('SUMARY_VARIO_OFF');
              
            } else {
              switch($plugs_info['PLUG_TYPE']) {
                case 'extractor':
                case 'intractor':
                case 'ventilator':
                case 'heating':
                    $unity="°C";
                    break;
                case 'pumpfilling':
                case 'pumpempting':
                case 'pump':
                    $unity="cm";
                    break;
                case 'humidifier': 
                case 'dehumidifier':
                    $unity="%";
                    break;
                default :
                    $unity="";
                    break;
              }
              $val_on=__('SUMARY_REGUL_ON');
              $val_off=__('SUMARY_REGUL_OFF');
            }
            $actions=array();
            $actions=explode('[',$plugs_info["data"]);
            $prev_value="0";
            $value=array();
            foreach($actions as $action) {
                $action=str_replace('],','',$action);
                $action=str_replace(']','',$action);
                $action=explode(',',$action);
                if((isset($action[0]))&&(isset($action[1]))&&(strcmp($action[0],"")!=0)&&(strcmp($action[1],"")!=0)) {
                      $heure=date ("H:i:s", $action[0]/1000);

                      if(strcmp("$prev_value","$action[1]")!=0) {
                          if(strcmp($action[1],"0")!=0) {
                            if(strcmp($resume[$number],"")==0) {
                                        if(strcmp($action[1],"99.9")==0) {
                                            $resume[$number]="<p align='center'><b><i>".__('SUMARY_TITLE')." ".$number.":<br /> ".$plugs_info['PLUG_NAME']."</i></b></p><p align='left'>".__('SUMARY_ON')." ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        } else {
                                            $resume[$number]="<p align='center'><b><i>".__('SUMARY_TITLE')." ".$number.":<br /> ".$plugs_info['PLUG_NAME']."</i></b></p><p align='left'>".$val_on." (".$action[1].$unity.") ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        }
                                    } else {
                                        if(strcmp($action[1],"99.9")==0) {
                                            $resume[$number]=$resume[$number].__('SUMARY_ON')." ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        } else {
                                            $resume[$number]=$resume[$number].$val_on." (".$action[1].$unity.") ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        }
                                    }
                                    $prev_value=$action[1];
                            } else if(strcmp($prev_value,"0")!=0) {
                                        if(strcmp("$prev_value","99.9")==0) {
                                            $resume[$number]=$resume[$number].__('SUMARY_OFF')." ".__('SUMARY_HOUR')." ".$heure."<br />";
                                        } else {
                                            $resume[$number]=$resume[$number].$val_off." ".__('SUMARY_HOUR')." ".$heure."<br />";

                                        }
                                            $prev_value=0;
                            } 
                        }
                    } 
                }

                if(strcmp($resume[$number],"")==0) { 
                        $resume[$number]="<p align='center'><b><i>".__('SUMARY_TITLE')." ".$number.": <br />".$plugs_info['PLUG_NAME']."</i></b></p><p align='center'>".__('EMPTY_ACTION')."</p>";
                } else {
                        $resume[$number]=$resume[$number]."</p>";
                }

            }
       $number=$number+1;
    }
    return $resume;
}
// }}}


// {{{ check_format_time()
// ROLE check time format: HH:MM:SS
// IN   $time      time to be checked
// RET 1 if ok, 0 if there is an error 
function check_format_time($time="") {
    if(strlen("$time")!=8) {
         return 0;
    }

    if(!preg_match('#^[0-2][0-9]:[0-5][0-9]:[0-5][0-9]$#', $time)) {
         return 0;
    }

    return 1;
}
// }}}


// {{{ get_browser_infos()
// ROLE get client browser informations
// RET array containing browser datas
function get_browser_infos() {
 $browser = array(
    'version'   => '0.0.0',
    'majorver'  => 0,
    'minorver'  => 0,
    'build'     => 0,
    'name'      => 'unknown',
    'useragent' => ''
  );

  $browsers = array(
    'firefox', 'msie', 'opera', 'chrome', 'safari', 'mozilla', 'seamonkey', 'konqueror', 'netscape',
    'gecko', 'navigator', 'mosaic', 'lynx', 'amaya', 'omniweb', 'avant', 'camino', 'flock', 'aol','mozilla'
  );

  if (isset($_SERVER['HTTP_USER_AGENT'])) {
    $browser['useragent'] = $_SERVER['HTTP_USER_AGENT'];
    $user_agent = strtolower($browser['useragent']);
    foreach($browsers as $_browser) {
      if (preg_match("/($_browser)[\/ ]?([0-9.]*)/", $user_agent, $match)) {
        $browser['name'] = $match[1];
        $browser['version'] = $match[2];
        @list($browser['majorver'], $browser['minorver'], $browser['build']) = explode('.', $browser['version']);
        break;
      }
    }
  }
  return $browser;
}
// }}}


// {{{ check_browser_compat()
// ROLE check if the client browser in compatible with the cultibox
// RET true if compat, false else
function check_browser_compat($tab) {
    if(count($tab)>0) {
        switch($tab['name']) {
            case 'firefox':
                //Do not support firefox 1.0 and 2.0
                if($tab['majorver']<=2) return false;
                return true;
                break;
            case 'mozilla':
                //Do not support firefox 1.0 and 2.0
                if($tab['majorver']<=2) return false;
                return true;
                break;
            case 'msie':
                //Do not support IE 6.0 or earlier
                if($tab['majorver']<=6) return false;
                return true;
                break;
            case 'chrome':
                //Support every version of chrome
                return true;
                break;
            case 'safari':
		        //Support for Mac Os X Safari
		        if($tab['majorver']<500) return false;
		        return true;
		        break;
        }
        return false;
    }
    return true;
}
// }}}


// {{{ get_sensor_type()
// ROLE get sensor type from the index file for the current day
// IN $nmb      number of the sensor 
//    $sd_card  path to the SD card plugged
//    $month    month to be checked
//    $day      day to be checked
// RET sensor type array

// In the index file:
//    '0' => 'none',
//    '1' => '',
//    '2' => 'tem_humi',
//    '3' => 'water_temp',
//    '4' => '',
//    '5' => 'wifi',
//    '6' => 'water_level',
//    '7' => 'water_level',
//    '8' => 'ph',
//    '9' => 'ec',
//    ':' => 'od',
//    ';' => 'orp'
function get_sensor_type($sd_card,&$sensor_type) {
    
    // Define index file
    $file="$sd_card/logs/index";

    //Si on n'arrive pas à trouver le fichier index définissant les capteurs, on renvoie false
    if(!file_exists($file)) 
        return false; 
  
    //On récupère le contenu du fichier index et on calcul la ligne de la journée actuelle: 
    $index_array = file($file);
    
    // Init return array (Why -6 : -4 For month and date AND -2 for \r\n)
    $sensor_type = array();
    for($i=0; $i<$GLOBALS['NB_MAX_SENSOR_PLUG'];$i++)
        $sensor_type[$i+1]="0";

    // Parse file
    // Foreach line of the file
    foreach ($index_array as $line) {
        $line=trim($line);
        if(strlen($line)==10) {
            // Next elements are sensors type
            $sensor_of_the_day=str_split(substr($line,4, -2), 1);

            // Update sensor_type array
            $index = 0;
            foreach($sensor_of_the_day as $type) {
                if($index>$GLOBALS['NB_MAX_SENSOR_PLUG']) break;
                // If type is not null and exists, update return array
                if ($type != "0" && $type != "" && array_key_exists("$type", $GLOBALS['SENSOR_DEFINITION'])) {
                    $sensor_type[$index + 1]=$type;
                
                    $index = $index + 1;
                }
            }
        }
    }
    return true;
}
// }}}


//{{{ translate_PlugType()
// ROLE Translate plug type
// Ajout de 'html' pour affichage correcte dans le graph des programmes de la légende
// IN plug
// RET plug translated
function translate_PlugType ($plug) {

    $ret = array();

    switch($plug) {
        case 'other':
            $ret['translate'] = __('PLUG_UNKNOWN','html');
            break;
        case 'extractor' :
            $ret['translate'] = __('PLUG_EXTRACTOR','html');
            break;
        case 'intractor' :
            $ret['translate'] = __('PLUG_INTRACTOR','html');
            break;
        case 'ventilator': 
            $ret['translate'] =__('PLUG_VENTILATOR','html');
            break;
        case 'heating': 
            $ret['translate'] = __('PLUG_HEATING','html');
            break;
        case 'pump': 
            $ret['translate'] = __('PLUG_PUMP','html');
            break;
        case 'pumpfilling' :
            $ret['translate'] = __('PLUG_PUMPFILLING','html');
            break;
        case 'pumpempting' :
            $ret['translate'] = __('PLUG_PUMPEMPTING','html');
            break;            
        case 'lamp': 
            $ret['translate'] = __('PLUG_LAMP','html');
            break;
        case 'humidifier': 
            $ret['translate'] = __('PLUG_HUMIDIFIER','html');
            break;
        case 'dehumidifier': 
            $ret['translate'] = __('PLUG_DEHUMIDIFIER','html');
            break;
        case 'electrovanne_co2': 
            $ret['translate'] = __('PLUG_CO2','html');
            break;
        default: 
            $ret['translate'] = __('PLUG_UNKNOWN','html');
            break;
    }

    return $ret['translate'];
}
//}}}


// {{{ generate_program_from_file()
//ROLE generate array containing data for a program from a file
// IN  $file         file to be read
//     $plug         plug id for the program
//     $idx          program_index
//     $out          error or warning message
// RET array containing program's data
function generate_program_from_file($file="",$plug,$idx=1,&$out) {
    $res=array();
    $handle=fopen("$file", 'r');
    if($handle) {
        while (!feof($handle)) {
            $buffer = fgets($handle);
            $temp = explode("\t", $buffer);
            if(count($temp)==4) {
                if(is_numeric($plug)) {
                    $res[]=array(
                        "selected_plug" => $plug,
                        "start_time" => $temp[0],
                        "end_time" => $temp[1],
                        "value_program" => $temp[2],
                        "type" =>  $temp[3],
                        "number" => $idx
                    );
                }
            }
        }
    }
    return $res;
}
//}}}


// {{{ advRmDir()
//ROLE remove directory and its contents
// IN  $dir     to bo be removed
// RET false if an error occured, true else
function advRmDir($dir) {
    // ajout du slash a la fin du chemin s'il n'y est pas
    if( !preg_match( "/^.*\/$/", $dir ) ) $dir .= '/';
 
    // Ouverture du repertoire demande
    $handle = @opendir( $dir );
 
    // si pas d'erreur d'ouverture du dossier on lance le scan
    if( $handle != false ) {
        // Parcours du repertoire
        while( $item = readdir($handle) ) {
            if($item != "." && $item != "..") {
                if( is_dir( $dir.$item ) ) advRmDir( $dir.$item );
                else @unlink( $dir.$item );
            }
        }
 
        // Fermeture du repertoire
        closedir($handle);
 
        // suppression du repertoire
        $res = @rmdir($dir);
    } else {
         $res = false;
    }
    return $res;
}


// {{{ parse_network_config()
// ROLE decode /etc/network/interfaces
// IN  
// RET array containing datas from /etc/network/interfaces ordered by interfaces
function parse_network_config() {
    $file="/etc/network/interfaces";
    $current=false; 
    $myArray=array();
    $myArray["first"]=array();

    if(file_exists($file)) {
        $config=file("$file");

        foreach($config as $conf) {
            $conf=trim($conf);
            $pos = strpos($conf, "#IFACE ");
            if($pos !== false) {
                  $current=substr($conf, 7,strlen($conf));
                  $myArray["${current}"]=array();
            } else {
                if($current) { 
                    $myArray["${current}"][]=$conf;
                } else {
                    $myArray["first"][]=$conf;
                }
            }
        }
    }
    return $myArray;
}
// }}}


// {{{ find_config()
// ROLE find configuration key in /Etc/netork/interface for a specific interface
// IN  $maArray     array containing value of the /etc/network/interfaces
//     $iface       interface to search
//     $key         key sentence to search
//     $type        val or bool to return the value or a boolean of the jey
// RET val or true or false depending configuration
function find_config($myArray,$iface,$key,$type="val") {
    if(count($myArray)<=1) return false;

    if(!array_key_exists(strtoupper($iface), $myArray)) return false;

    foreach($myArray[strtoupper($iface)] as $tab) {
        $pos=strpos($tab,$key);
        if($pos !== false) {
            if(strcmp($type,"val")==0) {
                $val=substr($tab, strlen($key)+1,strlen($tab)); 
                return $val;
            } else { 
                return true;
            }
        }

    }

    if(strcmp($type,"val")==0) {
        return "";
    } else {
        return false;
    }
}
//}}}


// {{{ get_phy_addr()
// ROLE get physical addr (MAC)
// IN  $iface   interface to check
// RET iface physical address
function get_phy_addr($iface) {
    $phy=exec("sudo /sbin/ifconfig ${iface}|/bin/grep HWaddr|/usr/bin/awk -F \" \" '{print $5}'");
    return trim($phy);
}
//}}}



// {{{ create_network_file()
// ROLE create the network configuration file
// IN  $myConf  array containing informations
// RET error number or -1 else
function create_network_file($myConf) {
    $route_gw=false;
    if(count($myConf)==0) return 2;
    
    $myArray=array();
    $myDns=array();
    $myArray[]="# interfaces(5) file used by ifup(8) and ifdown(8)";
    $myArray[]="";
    $myArray[]="#IFACE LO";
    $myArray[]="auto lo";
    $myArray[]="iface lo inet loopback";
    $myArray[]="";

    $myArray[]="#IFACE ETH0";
    $myArray[]="allow-hotplug eth0";

    if(strcmp($myConf['wire_type'],"static")==0) {
        $myArray[]="iface eth0 inet static";
        $myArray[]="address ".$myConf['wire_address'];
        $myArray[]="netmask ".$myConf['wire_mask'];

        if((strcmp($myConf['wire_gw'],"")!=0)&&(strcmp($myConf['wire_gw'],"0.0.0.0")!=0)) {
            $myArray[]="gateway ".$myConf['wire_gw'];
            $myArray[]="post-up /sbin/route add default gw ".$myConf['wire_gw']." eth0";
            $route_gw=true;  
        }


        $myDns[]="nameserver 208.67.222.222";
        $myDns[]="nameserver 208.67.220.220";


        
    } else {
        $myArray[]="iface eth0 inet dhcp";
    }
    $myArray[]="";



    
    if((strcmp($myConf['wifi_ssid'],"")!=0)||(strcmp($myConf['wifi_password'],"")!=0)) {
        $myArray[]="#IFACE WLAN0";
        $myArray[]="wireless-power off";
        $myArray[]="auto wlan0";
        $myArray[]="allow-hotplug wlan0";

        if(strcmp($myConf['wifi_type'],"static")==0) {
            $myArray[]="iface wlan0 inet static";
            $myArray[]="address ".$myConf['wifi_ip'];
            $myArray[]="netmask ".$myConf['wifi_mask'];

            if((strcmp($myConf['wifi_gw'],"")!=0)&&(strcmp($myConf['wifi_gw'],"0.0.0.0")!=0)) {
                $myArray[]="gateway ".$myConf['wifi_gw'];
                if(!$route_gw) {
                    $myArray[]="post-up /sbin/route add default gw ".$myConf['wifi_gw']." wlan0";
                    $myArray[]="post-down /bin/pkill -9 wpa_supplicant";
                }
            }


            if(count($myDns)==0) {
                $myDns[]="nameserver 208.67.222.222";
                $myDns[]="nameserver 208.67.220.220";
            }
        } else {
            $myArray[]="iface wlan0 inet dhcp";
        }

        switch($myConf['wifi_key_type']) {
            case "NONE":
                    $myArray[]="wireless-essid ".$myConf['wifi_ssid'];
                    $myArray[]="wireless-mode managed";
                    break;
            case "WEP":
                    $myArray[]="wireless-essid ".$myConf['wifi_ssid'];
                    if($myConf['hex_password']) {
                        $myArray[]="wireless-key ".$myConf['wifi_password'];
                    } else {
                        $hex="";
                        for ($i=0; $i<strlen($myConf['wifi_password']); $i++){
                            $ord = ord($myConf['wifi_password'][$i]);
                            $hexCode = dechex($ord);
                            $hex .= substr('0'.$hexCode, -2);
                        }    
                        $myArray[]="wireless-key ".$hex;
                    }
                    break;

            case "WPA AUTO":
                    $myArray[]="wpa-scan-ssid 1";
                    $myArray[]="wpa-ssid ".$myConf['wifi_ssid'];
                    $myArray[]="wpa-ap-scan 1";
                    $myArray[]="wpa-key-mgmt WPA-PSK";
                    $pwd=exec("/usr/bin/wpa_passphrase ".$myConf['wifi_ssid']." ".$myConf['wifi_password']."|/bin/grep psk=|/bin/grep -v \"#psk\"|/usr/bin/awk -F \"=\" '{print $2}'",$output,$err);
                    if(count($output)!=1) return 3;
                    $myArray[]="wpa-psk ".$output[0];
                    break;  
        }
    } else {
        //Setup of the cultipi_network conf:
        $myArray[]="#IFACE WLAN0";
        $myArray[]="auto wlan0";
        $myArray[]="allow-hotplug wlan0";
        $myArray[]="iface wlan0 inet static";
        $myArray[]="address 10.0.0.100";
        $myArray[]="netmask 255.0.0.0";
        $myArray[]="post-up /etc/rc.local; /etc/init.d/isc-dhcp-server force-reload; /etc/init.d/dnsmasq force-reload; /etc/init.d/hostapd force-reload";
    }


    if($f=fopen("/tmp/interfaces","w")) {
        foreach($myArray as $myInf) {
           fputs($f,"$myInf\n");
        }
    } else {
        return 4;
    }
    fclose($f);


    if(count($myDns)>0) {
        if($f=fopen("/tmp/resolv.conf","w")) {
            foreach($myDns as $myInf) {
                fputs($f,"$myInf\n");
            }
        } else {
            return 4;
        }
    }
    return 1;
}
// }}}

// {{{ getSocket()
// ROLE Return cocket number of cultipi module
// IN  $server   Server name
// RET socket number
function getSocket($server) {
    
    switch ($server) {
        case "serverCultipi":
            return 6000;
            break;
        case "serverLog":
            return 6003;
            break;
        case "serverPlugUpdate":
            return 6004;
            break;
        case "serverAcqSensor":
            return 6006;
            break;
        case "serverHisto":
            return 6009;
            break;
        case "serverIrrigation":
            return 6011;
            break;
        case "serverCultibox":
            return 6013;
            break;
        case "serverMail":
            return 6015;
            break;
        case "serverSupervision":
            return 6019;
            break;
        case "serverGet":
            return 6022;
            break;
        case "serverGetCommand":
            return 6023;
            break;
        case "serverSet":
            return 6024;
            break;
        case "serverSetCommand":
            return 6025;
            break;
        case "serverTrigger":
            return 6026;
            break;
        case "serverPHP":
            return 6027;
            break;
        default :
            return "NA";
            break;
    }
}
//}}}

// {{{ sendBySocket()
// ROLE Send data using socket
// IN  $server server name 
// IN  $ip ip
// IN  $message message to send
// RET Nothing
function sendBySocket($server, $ip, $message) {
    
        // Retrieve socket number
        $socketNumber = getSocket($server);
        $socketServerNumber = getSocket("serverPHP");
    
        // Init socket
        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        
        // create connection
        socket_connect($sock , $ip , $socketNumber);

        // send data
        $message = $socketServerNumber . " 0 " . $message;
        socket_send ( $sock , $message , strlen($message) , 0);
        
        socket_close($sock );
}
//}}}

// {{{ readBySocket()
// ROLE Send data using socket
// IN  $server server name 
// IN  $ip ip
// IN  $message message to send
// RET Reading value
function readBySocket($server, $ip, $message) {
    
        // Retrieve socket number
        $socketNumber = getSocket($server);
        $socketServerNumber = getSocket("serverPHP");
        
        // Init socket
        $sock1 = socket_create(AF_INET, SOCK_STREAM, 0);
        
        // create connection
        socket_connect($sock1 , $ip , $socketNumber);

        // send data
        $message = $socketServerNumber . " 0 " . $message;
        socket_send ( $sock1 , $message , strlen($message) , 0);
        
        socket_close($sock1 );
        
        
        // Create server for receiving data 
        if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
        {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
             
            die("Couldn't create socket: [$errorcode] $errormsg \n");
        }

         
        // Bind the source address
        if( !socket_bind($sock, $ip , $socketServerNumber) )
        {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
             
            socket_close($sock );
             
            die("Could not bind socket : [$errorcode] $errormsg \n");
        }

         
        if(!socket_listen ($sock , 10))
        {
            $errorcode = socket_last_error();
            $errormsg = socket_strerror($errorcode);
             
            socket_close($sock );
             
            die("Could not listen on socket : [$errorcode] $errormsg \n");
        }

        socket_set_nonblock ($sock);
         
        //start loop to listen for incoming connections
        $timeout = 4000;
        $startMillisensond = round(microtime(true) * 1000);
        while ((round(microtime(true) * 1000) - $startMillisensond) < $timeout) 
        {
            
            // Trick ...
            usleep(100);
            
            //Accept incoming connection - This is a blocking call
            $client =  socket_accept($sock);
            
            if ($client !== FALSE) {
                //read data from the incoming socket
                $input = socket_read($client, 1024000);
                
                socket_close($sock );
                
                return $input;
            }

            usleep(100);
        }
        
        return "TIMEOUT";
        
}
//}}}


// {{{ readBySocket()
// ROLE Send data using socket
// IN  $server server name
// IN  $ip ip
// IN  $message message to send
// RET Reading value
function get_reverse_webcam_port($webcam="") {
    if(strcmp("$webcam","")==0) {
       return ""; 
    }

    exec("ps aux|grep localhost:808".$webcam."|grep -v grep|head -1|awk -F \" \" '{print $17}'|awk -F \":\" '{print $1'}",$out,$err);
    if(count($out)==1) {
        $port=explode("-",$out[0]);
        return $port[$webcam-1];
    }

    return "";
     

}
// }}}

?>

