<?php
    if(count(get_included_files()) ==1) exit("Direct access not permitted.");
    
    class Parameters{
        const ZIP_URL = 'https://github.com/develost/_mywebsiteout/archive/master.zip';
        const ZIP_TEMP_NAME = 'website.zip';
        const VERSION_CHECK_URL = 'https://raw.githubusercontent.com/develost/_mywebsiteout/master/version.txt';
        const VERSION_TEMP_NAME = 'version.txt';
        const WEBSU_CHECK_URL = '';
        const USER = 'example';
        const PASSWORD = 'secret';
        const DATE_FORMAT = 'YmdHis';
        const USER_PARAM = 'user';
        const PASSWORD_PARAM = 'password';
        const HTACCESS_FILE = '.htaccess';
    };
?>

