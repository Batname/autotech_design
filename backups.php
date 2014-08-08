<?php
// php /var/www/autotech1.ua/backups.php in terminal

// Резервное копирование MySQL и файлов хостинга
// Версия 2.1 Яндекс
$dbhost = "localhost"; //Адрес MySQL сервера
$dbuser = "u_autotecht"; //Имя пользователя базы данных
$dbpass = "nLoEVc8Q"; //Пароль пользователя базы данных
$dbname = "autotech_new"; //Имя базы данных
$sitedir = "/var/www/autotech/autotech.ua"; //Абсолютный путь к сайту от корня диска
//$excludefile = $sitedir.'/backup/*.gz'; //Файлы которые не должны попасть в архив
$excludefile = $sitedir.'/var/*'; //Файлы которые не должны попасть в архив
$yadisk_email='dadubinin2@yandex.ua'; //Имя пользователя Яндекс.Диск
$yadisk_pass='21091091'; //Пароль пользователя Яндекс.Диск
$yadisc_dir='backups_autotech/'; //Директория на Яндекс.Диск куда будем копировать. Она должна существовать!
// Все что ниже, лучше не трогать
$dbbackup = $dbname .'_'. date("Y-m-d_H-i-s") . '.sql.gz';
$filebackup = 'files_'. date("Y-m-d_H-i-s") .'.tar.gz';
system("mysqldump -h $dbhost -u $dbuser --password='$dbpass' $dbname | gzip > $dbbackup");
//Для больших баз данных закоментировать строчку выше и раскоментировать ниже.
//system("mysqldump --quick -h $dbhost -u $dbuser --password='$dbpass' $dbname | gzip > $dbbackup");
system ("curl --user $yadisk_email:$yadisk_pass -T $dbbackup https://webdav.yandex.ru/$yadisc_dir");
unlink($dbbackup);
shell_exec("tar cvfz $filebackup $sitedir --exclude=$filebackup --exclude=$excludefile");
system ("curl --user $yadisk_email:$yadisk_pass -T $filebackup https://webdav.yandex.ru/$yadisc_dir");
unlink($filebackup);

if (isset($filebackup) && isset($dbbackup)) {
    echo "Success";
} else {
    echo "You have some errors";
}


?>
