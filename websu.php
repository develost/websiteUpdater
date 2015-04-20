<html>
<head>
<title>Websu - website updater</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<style>
    body{vertical-align:bottom;line-height:25px;font-family:Arial;background-color:#EEEEEE;}
    fieldset{background:#FFFFFF;color:#000000;border: solid 1px black;margin-bottom:20px;}
    legend{background:#0000EE;border: solid 1px black;color:#FFFFFF;padding:5px 20px 5px 20px;width:150px;}
    table{border-collapse:collapse;}
    table,th,td{border: 1px solid black;}
    th{background-color: green;color: white;}  
    td,th{padding:5px;}    
    input[type=submit] {width: 100%;height:30px;}    
    a{color:#000000;text-decoration:none;}
    a:hover{text-decoration:underline;}
    .header{padding-top:10px;text-align:center;}
    .header h1{}
    .main{padding-top:10px;}
    .left{float:left;}
    .right{float:right;}
    .update{background:#EE0000;}
    .clear{clear:both;}
    .center{text-align:center;}
</style>
</head>
<body>
<div class="header">
    <a href="http://www.develost.com/websu"><h1>Websu</h1></a>
    <h2>website updater</h2>
</div>
<div class="main">
<?php
include_once "parameters.php";

class Constants{
    const WEBSU_CURRENT_VERSION = '0.0.7';
    const WEBSU_VERSION_CHECK_URL = 'https://raw.githubusercontent.com/develost/websu/master/version.txt';
    //const WEBSU_VERSION_CHECK_URL = 'https://www.develost.com/apps/websuversion';
    const WEBSU_FILE_URL = 'https://raw.githubusercontent.com/develost/websu/master/websu.php';
};

/*************************************************************************************************
 * 
 *************************************************************************************************/
function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

// TODO 1: handle error of file_get_contents AND file_put_contents 
// Both return FALSE on failure
// Step 1: check the return code: if($content === FALSE) { // handle error here... }
// Step 2: suppress the warning by putting an @ in front of the file_get_contents: $content = @file_get_contents($site);



/*************************************************************************************************
 * 
 *************************************************************************************************/
function updateWebsite(){
    if (file_exists (Parameters::WEBSITE_ZIP_TEMP_NAME)){
        unlink(Parameters::WEBSITE_ZIP_TEMP_NAME);
    }
    
    $remoteVersion = file_get_contents(Parameters::WEBSITE_VERSION_CHECK_URL);
    $localVersion = "UNDEF";
    if (file_exists (Parameters::WEBSITE_VERSION_TEMP_NAME)){
        $localVersion = file_get_contents(Parameters::WEBSITE_VERSION_TEMP_NAME);
    }
    
    if ($remoteVersion == $localVersion){
        echo "remoteVersion " . $remoteVersion . " equal to localVersion " . $localVersion;
        return;
    }else{
        echo "Migrating from " . $localVersion . " to " . $remoteVersion . "<br>";
    }
    
    $now = date(Parameters::GENERAL_DATE_FORMAT);
    echo "Now: " . $now . "<br>";
    // get remote file to local string
    $zipString = file_get_contents(Parameters::WEBSITE_ZIP_URL);
    // write string to local file
    file_put_contents(Parameters::WEBSITE_ZIP_TEMP_NAME,$zipString);
    $zip = zip_open(Parameters::WEBSITE_ZIP_TEMP_NAME);
    $websiteRoot = NULL;
    echo '<table><tr><th class="center">Type</th><th class="center">Name</th></tr>'. PHP_EOL;
    $fileCounter = 0;
    $dirCounter = 0;
    if (is_resource($zip)){
        while ($zip_entry = zip_read($zip)){
            echo '<tr><td class="center">';
            if (zip_entry_open($zip, $zip_entry)){
                $zipEntryName = zip_entry_name($zip_entry);
                $zipEntryName = $now . '-' . $zipEntryName;
                if (is_null($websiteRoot)){
                    $websiteRoot = $zipEntryName;
                }
                $zipEntryContents = zip_entry_read($zip_entry,zip_entry_filesize($zip_entry));
                if (endsWith($zipEntryName,"/")){
                    //echo $zipEntryName . "D";
                    echo "D";
                    $dirCounter ++;
                    if (!file_exists($zipEntryName)){
                        mkdir($zipEntryName, 0777, true);
                    }
                    
                } else { 
                    //echo $zipEntryName . "F";
                    echo "F";
                    $fileCounter ++;
                    if (0 != strcmp(Parameters::WEBSITE_CRYPTO_KEY,'')){
                        list($hmac, $iv, $encrypted)= explode(':',$zipEntryContents);
                        $iv = base64_decode($iv);
                        $encrypted = base64_decode($encrypted);
                        $macKey = mhash_keygen_s2k(MHASH_SHA256, $key, $iv, 32);
                        $newHmac= hash_hmac('sha256', $iv . MCRYPT_BLOWFISH . $encrypted, $macKey);
                        if ($hmac!==$newHmac) {
                            die('Authentication error: check crypto key.');
                        }
                        $decrypt = mcrypt_decrypt(
                            MCRYPT_BLOWFISH,
                            $key,
                            $encrypted,
                            MCRYPT_MODE_CBC,
                            $iv
                        );
                        $zipEntryContents = rtrim($decrypt, "\0");                     
                    }
                    if (file_exists($zipEntryName)){
                        unlink($zipEntryName);
                    }
                    file_put_contents($zipEntryName,$zipEntryContents);   
                }
                zip_entry_close($zip_entry);
            }
            echo "</td><td>" . zip_entry_name($zip_entry) . "</td></tr>". PHP_EOL;
        }
        zip_close($zip);
    }
    echo "</table>". PHP_EOL;
    echo "Written " . $dirCounter . " dirs and " . $fileCounter . " files".PHP_EOL;
    unlink(Parameters::WEBSITE_ZIP_TEMP_NAME);


    $htaccessContent = "";
    $htaccessContent.= "# ------------------------------------------------" . PHP_EOL;
    $htaccessContent.= "# Created on " . $now . PHP_EOL;
    $htaccessContent.= "# ------------------------------------------------" . PHP_EOL;
    $htaccessContent.= "<IfModule mod_rewrite.c>" . PHP_EOL;
    $htaccessContent.= "    RewriteEngine On" . PHP_EOL;
    $htaccessContent.= "    RewriteCond %{REQUEST_URI} ^". Parameters::WEBSITE_ROOT . "websu.php" . PHP_EOL;
    $htaccessContent.= "    RewriteRule ^(.*)$ websu.php [L]" . PHP_EOL;
    $htaccessContent.= Parameters::WEBSITE_CUSTOM_REDIRECT;
    $htaccessContent.= "    RewriteCond %{REQUEST_URI} !^" . Parameters::WEBSITE_ROOT . $websiteRoot . PHP_EOL;
    $htaccessContent.= "    RewriteRule ^(.*)$ " . Parameters::WEBSITE_ROOT . $websiteRoot . "$1 [L]" . PHP_EOL;
    $htaccessContent.= "</IfModule>";

    file_put_contents(Parameters::GENERAL_HTACCESS_FILE,$htaccessContent);
    file_put_contents(Parameters::WEBSITE_VERSION_TEMP_NAME,$remoteVersion);
}

/*************************************************************************************************
 * 
 *************************************************************************************************/
function updateWebsu(){
    $remoteVersion = file_get_contents(Constants::WEBSU_VERSION_CHECK_URL);
    $localVersion = Constants::WEBSU_CURRENT_VERSION;
    if ($remoteVersion == $localVersion){
        echo "remoteVersion " . $remoteVersion . " equal to localVersion " . $localVersion;
        return;
    }else{
        echo "Migrating from " . $localVersion . " to " . $remoteVersion . "<br>";
    }
    $websuString = file_get_contents(Constants::WEBSU_FILE_URL);
    // write string to local file
    //file_put_contents("websu" . $remoteVersion . ".php",$websuString);
    file_put_contents("websu.php",$websuString);
}

/*************************************************************************************************
 * 
 *************************************************************************************************/
function presentLoginForm(){
    $localVersion = "UNDEF";
    if (file_exists (Parameters::WEBSITE_VERSION_TEMP_NAME)){
        $localVersion = file_get_contents(Parameters::WEBSITE_VERSION_TEMP_NAME);
    }        
    echo '<form action="" method="post">' . PHP_EOL;
    echo '    <fieldset>'.PHP_EOL;
    echo '        <legend>Status</legend>'.PHP_EOL;
    echo '        <p class="clear"><label class="left">My website version </label>   <input class="right" type="text" value="' . $localVersion . '" disabled></p>' . PHP_EOL;
    echo '        <p class="clear"><label class="left">Websu version</label>         <input class="right" type="text" value="' . Constants::WEBSU_CURRENT_VERSION . '" disabled></p>' . PHP_EOL;
    echo '    </fieldset>'.PHP_EOL;
    echo '    <fieldset>'.PHP_EOL;
    echo '        <legend>Authentication</legend>'.PHP_EOL;
    echo '        <p class="clear"><label class="left">Username</label>              <input class="right" type="text" id="'.Parameters::GENERAL_USER_PARAM.'" name="'.Parameters::GENERAL_USER_PARAM.'" required></p>' . PHP_EOL;
    echo '        <p class="clear"><label class="left">Password</label>              <input class="right" type="password" id="'.Parameters::GENERAL_PASSWORD_PARAM.'" name="'.Parameters::GENERAL_PASSWORD_PARAM.'" required></p>' . PHP_EOL;
    echo '    </fieldset>'.PHP_EOL;
    echo '    <fieldset>'.PHP_EOL;
    echo '        <legend class="update">Update</legend>'.PHP_EOL;
    echo '        <p class="clear"><label class="left">My website</label>            <input class="left" type="radio" name="'.Parameters::GENERAL_WHAT_PARAM.'" value="'.Parameters::GENERAL_WHAT_MYWEBSITE.'" checked="checked"/>'.PHP_EOL;
    echo '        <label class="right">Websu</label>                <input class="right" type="radio" name="'.Parameters::GENERAL_WHAT_PARAM.'" value="'.Parameters::GENERAL_WHAT_WEBSU.'"/></p>'.PHP_EOL;
    echo '        <p class="clear"><input type="submit" id="submit" name="submit" value="Start"></p>' . PHP_EOL;
    echo '    </fieldset>'.PHP_EOL;
    echo '    <fieldset>'.PHP_EOL;
    echo '        <legend>Resources</legend>'.PHP_EOL;
    echo '        <p class="clear"><a class="left" href="'. Parameters::WEBSITE_ROOT . '">my website root</a>'.PHP_EOL;
    echo '        <a class="right" href="http://www.develost.com/websu">websu home page</a></p>'.PHP_EOL;
    echo '    </fieldset>'.PHP_EOL;
    echo '</form>'.PHP_EOL;
}


/*************************************************************************************************
 * 
 *************************************************************************************************/
function main(){
    if (isset($_POST[Parameters::GENERAL_USER_PARAM]) &&  isset($_POST[Parameters::GENERAL_PASSWORD_PARAM])){
        if ((Parameters::WEBSITE_UPDATE_USER == $_POST[Parameters::GENERAL_USER_PARAM]) && (Parameters::WEBSITE_UPDATE_PASSWORD == $_POST[Parameters::GENERAL_PASSWORD_PARAM] )){
            // user and password are correct
            // check if update website or websu
            if ($_POST[Parameters::GENERAL_WHAT_PARAM] == Parameters::GENERAL_WHAT_MYWEBSITE){
                updateWebsite();
            }else if ($_POST[Parameters::GENERAL_WHAT_PARAM] == Parameters::GENERAL_WHAT_WEBSU){
                updateWebsu();
            }else {
                echo '<div>Unknown update type</div>';
            }
        }else{
            // login incorrect
            echo '<div>Login error</div>';
        }
    }else{
        // present the login form
        presentLoginForm(); 
    }
}



main();
?>
</div>
</body>
</html>