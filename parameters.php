<?php
    if(count(get_included_files()) == 1) exit("Direct access not permitted.");
    
    define('_CUSTOM_REDIRECT_', '');
    
    class Parameters{
        const WEBSITE_ZIP_URL = 'https://raw.githubusercontent.com/develost/websu/master/example/example_website.zip';
        const WEBSITE_ZIP_TEMP_NAME = 'example_website.zip';
        const WEBSITE_VERSION_CHECK_URL = 'https://raw.githubusercontent.com/develost/websu/master/example/example_version.txt';
        const WEBSITE_VERSION_TEMP_NAME = 'version.txt';
        const WEBSITE_ROOT = '/';
        const WEBSITE_UPDATE_USER = 'example';
        const WEBSITE_UPDATE_PASSWORD = 'secret';
        const WEBSITE_CUSTOM_REDIRECT = _CUSTOM_REDIRECT_;
        //const WEBSITE_CRYPTO_KEY = 'SAGFo92jzVnzSj39IUYGvi4eL8v2RvJG8Cytuiouh147vCytdyWFl91R';
        const WEBSITE_CRYPTO_KEY = '';
        const GENERAL_DATE_FORMAT = 'YmdHis';
        const GENERAL_USER_PARAM = 'user';
        const GENERAL_PASSWORD_PARAM = 'password';
        const GENERAL_WHAT_PARAM = 'what';
        const GENERAL_WHAT_MYWEBSITE = 'mywebsite';
        const GENERAL_WHAT_WEBSU = 'websu';
        const GENERAL_HTACCESS_FILE = '.htaccess';
        
    };
?>