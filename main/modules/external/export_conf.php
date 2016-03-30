<?php

require_once('../../libs/config.php');

$file="../../../tmp/export/backup_bulckypi.sql";


if(is_file("$file")) exec("mv $file /tmp/",$output,$err);
if(is_file("$file.zip")) exec("mv $file.zip /tmp/",$output,$err);

exec("/usr/bin/mysqldump --defaults-extra-file=/var/www/bulcky/sql_install/my-extra.cnf -h 127.0.0.1 --port=3891 bulcky --ignore-table=bulcky.logs --ignore-table=bulcky.power > /var/www/bulcky/tmp/export/backup_bulckypi.sql",$output,$err);
exec("/usr/bin/mysqldump --defaults-extra-file=/var/www/bulcky/sql_install/my-extra.cnf -h 127.0.0.1 --port=3891 bulcky --no-data logs >> /var/www/bulcky/tmp/export/backup_bulckypi.sql",$output,$err);
exec("/usr/bin/mysqldump --defaults-extra-file=/var/www/bulcky/sql_install/my-extra.cnf -h 127.0.0.1 --port=3891 bulcky --no-data power >> /var/www/bulcky/tmp/export/backup_bulckypi.sql",$output,$err);
exec("zip -j ../../../tmp/export/backup_bulckypi.sql.zip $file",$output,$err);

if(is_file("$file.zip")) {
    echo json_encode("2");
} else if(is_file("$file")) {
    echo json_encode("1");
} else {
    echo json_encode("0");
}

?>
