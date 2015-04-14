#!/bin/sh
locale-gen en_IE.UTF-8
locale-gen nl_BE.UTF-8
apt-get install wget ca-certificates
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | apt-key add -
# PHP PPA key
apt-key adv --keyserver keyserver.ubuntu.com --recv-keys 4F4EA0AAE5267A6C
add-apt-repository -y ppa:ondrej/php5-5.6
sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt trusty-pgdg main" >> /etc/apt/sources.list'
apt-key update
apt-get update
apt-get -y install git postgresql-9.4 postgresql-contrib postgresql-9.4-postgis-2.1 curl apache2 php5 libapache2-mod-php5 php5-cli php5-pgsql php5-fpm php5-curl php5-mcrypt php5-intl php5-xdebug nodejs npm
pg_createcluster 9.4 main --start
useradd -s /bin/bash -m -d /home/tvdw tvdw
usermod -aG www-data vagrant
usermod -aG www-data tvdw

# create tvdw database
sudo -u postgres psql << EOF
create user tvdw with password 'tvdw';
create database tvdw owner tvdw;
EOF

echo "Creating PostGIS Extension"
sudo -u postgres psql tvdw << EOF
create extension postgis;
EOF

curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
ln -s /usr/bin/nodejs /usr/bin/node
npm install -g bower

mv /var/www /var/www-old
ln -s /vagrant/web /var/www

cp /vagrant/config/apache.conf /etc/apache2/sites-available/tvdw.conf
a2dissite 000-default
a2ensite tvdw
service apache2 restart