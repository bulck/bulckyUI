<?php

require_once('../../libs/config.php');


if((isset($_GET['filename']))&&(!empty($_GET['filename']))) {
    $file="../../../tmp/import/".$_GET['filename'];
    if(is_file("$file")) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE); 
        $extension=finfo_file($finfo, "$file");
        finfo_close($finfo);
    
        if((strcmp("$extension","application/zip")==0)||(strcmp("$extension","multipart/x-zip")==0)) {
              exec("/usr/bin/unzip -o \"$file\" -d ../../../tmp/import/|/bin/grep inflating|/usr/bin/awk -F\": \" '{print $2}'",$output,$err);
              if(trim($output[0])!="") {
                  $file=trim($output[0]);
              } else {
                  echo json_encode("1");
                  return 0;
              }
        } elseif((strcmp("$extension","multipart/x-gzip")==0)||(strcmp("$extension","application/x-gzip")==0)) {
              exec("/bin/gunzip -l \"$file\" |/bin/grep -v \"compressed\"|/usr/bin/awk -F \" \" '{print $4}'",$output,$err);
              if(trim($output[0])!="") {
                  $import=trim($output[0]);
              } else {
                  echo json_encode("1");
                  return 0;
              }
                      
              exec("/bin/zcat -f \"$file\" > \"$import\"",$output,$err);
              $file=$import;
        } 
        exec("/usr/bin/mysql --defaults-extra-file=/var/www/bulcky/sql_install/my-extra.cnf -h 127.0.0.1 --port=3891 bulcky < \"$file\"",$output,$err);

        if($err!=0) {
             echo json_encode("1");
             return 0;
         } else {
             echo json_encode("0");
             return 0;
         }
    }
}
echo json_encode("1");

?>
