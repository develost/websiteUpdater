# websu

## Update my website minimizing offline time
Websu is designed specifically for this:
 - it prepares a fresh copy of your website
 - and switches to the new version only when the copy of all the files is done
 - it works under apache httpd, does not work for IIS

## Quick test
TODO

 
## Initial setup
 - websu is written in PHP so make sure your web server support php (version 5) language
 - enable the following in php.ini
```ini
    allow_url_fopen = On
``` 
 - make sure php can write and modify file and directories 
 - copy websu.php into the root folder of your website
 - zip your pages and put somewere, can be a dropbox link or whatever
 - make a text file with the version name as the content and put somewhere
 - copy and modify parameters.php
 - test installation http://\<your-website\>/websu.php and update website

 
## Some capabilities
 - autoupdate: only few clicks and get last version
 - update webite only in case of new version available
 - user/password protection
 - shows nicely on mobile

## Screenshots
![websu main page](https://github.com/develost/websu/raw/master/websu_main_page.jpg "websu main page")
![websu file page](https://github.com/develost/websu/raw/master/websu_file_page.jpg "websu file page")



TODO 