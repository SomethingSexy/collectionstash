#!/bin/bash
cd /usr/src/collectionstash;
git pull;
# copy over source
cp  -r /usr/src/collectionstash/app/* /var/www/collectionstash/app;
cp  -r /usr/src/collectionstash/plugins/* /var/www/collectionstash/plugins;
cp  -r /usr/src/collectionstash/vendors/* /var/www/collectionstash/vendors;

# restart web servers
/etc/init.d/apache2 restart;
/etc/init.d/nginx restart;
