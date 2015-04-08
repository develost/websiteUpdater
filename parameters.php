<?php
    if(count(get_included_files()) == 1) exit("Direct access not permitted.");
    
    class Parameters{
        const WEBSITE_ZIP_URL = 'https://github.com/develost/_mywebsiteout/archive/master.zip';
        const WEBSITE_ZIP_TEMP_NAME = 'website.zip';
        const WEBSITE_VERSION_CHECK_URL = 'https://raw.githubusercontent.com/develost/_mywebsiteout/master/version.txt';
        const WEBSITE_VERSION_TEMP_NAME = 'version.txt';
        const WEBSITE_ROOT = '/';
        const WEBSITE_UPDATE_USER = 'example';
        const WEBSITE_UPDATE_PASSWORD = 'secret';

        const GENERAL_DATE_FORMAT = 'YmdHis';
        const GENERAL_USER_PARAM = 'user';
        const GENERAL_PASSWORD_PARAM = 'password';
        const GENERAL_WHAT_PARAM = 'what';
        const GENERAL_WHAT_MYWEBSITE = 'mywebsite';
        const GENERAL_WHAT_WEBSU = 'websu';
        const GENERAL_HTACCESS_FILE = '.htaccess';
        
    };
?>

