#collectionstash


##Installation

### Library Dependencies
* [WAMP](http://www.wampserver.com/en/) (or really any webserver but WAMP is easiest)
* MySQL 5.6.x (see WAMP)
* PHP 5.4.x (see WAMP)
* npm
* [Bower](http://bower.io/)
* [Composer](https://getcomposer.org/doc/00-intro.md) 
* [CakePHP](http://book.cakephp.org/2.0/en/index.html) 2.6.x 

### Installing project

Clone this repo

    git clone https://github.com/SomethingSexy/collectionstash

Run npm in project root.  That should also pull down all bower dependencies.
    
    npm install

Run composer install.
    
    composer install

Clone Cakephp version 2.6.x to the "lib" directly in the root.  I don't think the project has one, so make the directory first.  Should end up being collectionstash/lib/Cake

### Configuration
#### Apache

httpd.conf

    DocumentRoot "F:/Development/projects/collectionstash/app/webroot"
    <Directory "F:/Development/projects/collectionstash">
        Options Indexes FollowSymLinks
        AllowOverride All
        Order Deny,Allow
        Deny from all
        Allow from 127.0.0.1
        Allow from ::1
        Allow from localhost
    </Directory>

#### PHP
Make sure php_soap, php_curl, and php_openssl extensions are enabled.

