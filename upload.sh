#!/bin/sh
####################################
#
# Convert SH.
#
####################################

if [ -f /var/www/autotech/autotech.ua/var/import/import-1c.csv ];
then
iconv -f WINDOWS-1251 -t UTF-8 /var/www/autotech/autotech.ua/var/import/import-1c.csv > /var/www/autotech/autotech.ua/var/import/import-1c-utf8.csv
/usr/bin/php /var/www/autotech/autotech.ua/magmi/cli/magmi.cli.php -mode=create -profile=1c_import -f
/usr/bin/php /var/www/autotech/autotech.ua/shell/indexer.php --reindexall -f
fi


