# Air-Sal
## A super great website

---

## Initial setup:

- Install Virtualbox then Vagrant

`> vagrant up`

`> vagrant ssh`

`> sudo add-apt-repository ppa:ondrej/php`

`> sudo apt-get update`

`> sudo apt install --no-install-recommends apache2 php7.1 libapache2-mod-php7.1 php7.1-mysql php7.1-curl php7.1-json php7.1-gd php7.1-mcrypt php7.1-msgpack php7.1-memcached php7.1-intl php7.1-sqlite3 php7.1-gmp php7.1-geoip php7.1-mbstring php7.1-redis php7.1-xml php7.1-zip mysql-server`

Mysql password for root access must be `0000`

`> php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"`

`> php composer-setup.php`

`> php -r "unlink('composer-setup.php');"`

`> sudo mv composer.phar /usr/local/bin`

`> cd /var/www/symfony/air-sal`

`> composer.phar install`

`> php bin/console doctrine:schema:update -f`

Then install node in any way you want, then less as a global package 

`> cd public/css`

`> lessc main.less main.css`

`> cd /var/www/symfony/air-sal`

`> php bin/console server:run`

