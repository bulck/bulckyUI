<?php

require_once('../../libs/config.php');

$file="../../../tmp/export/backup_cultibox.sql";


if(isset($GLOBALS['MODE']) && $GLOBALS['MODE'] == "cultipi") {
    if(is_file("$file")) exec("mv $file /tmp/",$output,$err);
    if(is_file("$file.zip")) exec("mv $file.zip /tmp/",$output,$err);

    exec("/usr/bin/mysqldump --defaults-extra-file=/var/www/bulcky/sql_install/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox --ignore-table=cultibox.logs --ignore-table=cultibox.power > /var/www/bulcky/tmp/export/backup_cultibox.sql",$output,$err);
    exec("/usr/bin/mysqldump --defaults-extra-file=/var/www/bulcky/sql_install/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox --no-data logs >> /var/www/bulcky/tmp/export/backup_cultibox.sql",$output,$err);
    exec("/usr/bin/mysqldump --defaults-extra-file=/var/www/bulcky/sql_install/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox --no-data power >> /var/www/bulcky/tmp/export/backup_cultibox.sql",$output,$err);
    exec("zip -j ../../../tmp/export/backup_cultibox.sql.zip $file",$output,$err);
} else {
    $os=php_uname('s');
    switch($os) {
        case 'Linux':
            exec("/opt/bulcky/bin/mysqldump --defaults-extra-file=/opt/bulcky/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox --ignore-table=cultibox.logs --ignore-table=cultibox.power > /opt/bulcky/htdocs/bulcky/tmp/export/backup_cultibox.sql",$output,$err);
            exec("/opt/bulcky/bin/mysqldump --defaults-extra-file=/opt/bulcky/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox --no-data power >> /opt/bulcky/htdocs/bulcky/tmp/export/backup_cultibox.sql",$output,$err);
            exec("/opt/bulcky/bin/mysqldump --defaults-extra-file=/opt/bulcky/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox --no-data logs >> /opt/bulcky/htdocs/bulcky/tmp/export/backup_cultibox.sql",$output,$err);
            break;
        case 'Mac':
        case 'Darwin':
            if(is_file("$file.zip")) exec("mv $file.zip /tmp/",$output,$err);
            exec("/Applications/bulcky/xamppfiles/bin/mysqldump --defaults-extra-file=/Applications/bulcky/xamppfiles/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox --ignore-table=cultibox.logs --ignore-table=cultibox.power > /Applications/bulcky/xamppfiles/htdocs/bulcky/tmp/export/backup_cultibox.sql",$output,$err);
            exec("/Applications/bulcky/xamppfiles/bin/mysqldump --defaults-extra-file=/Applications/bulcky/xamppfiles/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox --no-data power >> /Applications/bulcky/xamppfiles/htdocs/bulcky/tmp/export/backup_cultibox.sql",$output,$err);
            exec("/Applications/bulcky/xamppfiles/bin/mysqldump --defaults-extra-file=/Applications/bulcky/xamppfiles/etc/my-extra.cnf -h 127.0.0.1 --port=3891 cultibox --no-data logs >> /Applications/bulcky/xamppfiles/htdocs/bulcky/tmp/export/backup_cultibox.sql",$output,$err);
            exec("zip -j ../../../tmp/export/backup_cultibox.sql.zip $file",$output,$err);
            break;
        case 'Windows NT':
            exec("C:\\cultibox\\xampp\\mysql\\bin\\mysqldump.exe --defaults-extra-file=C:\\cultibox\\xampp\\mysql\\bin\\my-extra.cnf -h 127.0.0.1 --port=3891 cultibox --ignore-table=cultibox.logs --ignore-table=cultibox.power > C:\\cultibox\\xampp\\htdocs\\cultibox\\tmp\\export\\backup_cultibox.sql",$output,$err);
            exec("C:\\cultibox\\xampp\\mysql\\bin\\mysqldump.exe --defaults-extra-file=C:\\cultibox\\xampp\\mysql\\bin\\my-extra.cnf -h 127.0.0.1 --port=3891 cultibox --no-data power >> C:\\cultibox\\xampp\\htdocs\\cultibox\\tmp\\export\\backup_cultibox.sql",$output,$err);
            exec("C:\\cultibox\\xampp\\mysql\\bin\\mysqldump.exe --defaults-extra-file=C:\\cultibox\\xampp\\mysql\\bin\\my-extra.cnf -h 127.0.0.1 --port=3891 cultibox --no-data logs >> C:\\cultibox\\xampp\\htdocs\\cultibox\\tmp\\export\\backup_cultibox.sql",$output,$err);
            break;
    }
}




if(is_file("$file.zip")) {
    echo json_encode("2");
} else if(is_file("$file")) {
    echo json_encode("1");
} else {
    echo json_encode("0");
}

?>
