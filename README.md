# websu

## Update my website minimizing offline time
Websu is designed specifically for this:
 - it prepares a fresh copy of your website
 - and switches to the new version only when the copy of all the files is done
 - it works under *apache httpd*, does not work for IIS

## Quick start
 - download websu.php and parameters.php and put them into the root folder of yout website
 - user example password secret
 - run update website
 - go to root folder, you should see "It Works" page

### Problems?
 1. Check php.ini
```ini
    allow_url_fopen = On
```
 2. Check PHP version 5 or above 
 3. Check and eventually open issue here on github 
 
## Initial setup
 - websu is written in PHP so make sure your web server support php (version 5 or above) language
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

