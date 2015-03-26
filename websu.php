<html>
<head>
<title>Websu - for internal use only</title>
</head>
<body>
<?php
include_once "parameters.php";

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function updateWebsite(){
    if (file_exists (Parameters::ZIP_TEMP_NAME)){
        unlink(Parameters::ZIP_TEMP_NAME);
    }
    
    $remoteVersion = file_get_contents(Parameters::VERSION_CHECK_URL);
    $localVersion = "UNDEF";
    if (file_exists (Parameters::VERSION_TEMP_NAME)){
        $localVersion = file_get_contents(Parameters::VERSION_TEMP_NAME);
    }
    
    if ($remoteVersion == $localVersion){
        echo "remoteVersion " . $remoteVersion . " equal to localVersion " . $localVersion;
        return;
    }else{
        echo "Migrating from " . $localVersion . " to " . $remoteVersion . "<br>";
    }
    
    $now = date(Parameters::DATE_FORMAT);
    // get remote file to local string
    $zipString = file_get_contents(Parameters::ZIP_URL);
    // write string to local file
    file_put_contents(Parameters::ZIP_TEMP_NAME,$zipString);
    $zip = zip_open(Parameters::ZIP_TEMP_NAME);
    $websiteRoot = NULL;
    if (is_resource($zip)){
        while ($zip_entry = zip_read($zip)){
            echo "<p>";
            echo "Name: " . zip_entry_name($zip_entry) . "<br />";
            if (zip_entry_open($zip, $zip_entry)){
                $zipEntryName = zip_entry_name($zip_entry);
                $zipEntryName = $now . '-' . $zipEntryName;
                if (is_null($websiteRoot)){
                    $websiteRoot = $zipEntryName;
                }
                $zipEntryContents = zip_entry_read($zip_entry);
                if (endsWith($zipEntryName,"/")){
                    echo $zipEntryName . " is a dir";
                    mkdir($zipEntryName, 0777, true);
                } else { 
                    echo $zipEntryName . " is a file";
                    file_put_contents($zipEntryName,$zipEntryContents);   
                }
                zip_entry_close($zip_entry);
            }
            echo "</p>";
        }
        zip_close($zip);
    }
    unlink(Parameters::ZIP_TEMP_NAME);


    $htaccessContent = "";
    $htaccessContent.= "# ------------------------------------------------" . PHP_EOL;
    $htaccessContent.= "# Created on " . $now . PHP_EOL;
    $htaccessContent.= "# ------------------------------------------------" . PHP_EOL;
    $htaccessContent.= "<IfModule mod_rewrite.c>" . PHP_EOL;
    $htaccessContent.= "    RewriteEngine On" . PHP_EOL;
    $htaccessContent.= "    RewriteCond %{REQUEST_URI} ^/websu.php" . PHP_EOL;
    $htaccessContent.= "    RewriteRule ^(.*)$ $1 [L]" . PHP_EOL;
    $htaccessContent.= "    RewriteCond %{REQUEST_URI} !^/" . $websiteRoot . PHP_EOL;
    $htaccessContent.= "    RewriteRule ^(.*)$ ". $websiteRoot ."$1 [L]" . PHP_EOL;
    $htaccessContent.= "</IfModule>";

    file_put_contents(Parameters::HTACCESS_FILE,$htaccessContent);
    file_put_contents(Parameters::VERSION_TEMP_NAME,$remoteVersion);
}


function main(){
    if (isset($_POST[Parameters::USER_PARAM]) &&  isset($_POST[Parameters::PASSWORD_PARAM])){
        if ((Parameters::USER == $_POST[Parameters::USER_PARAM]) && (Parameters::PASSWORD == $_POST[Parameters::PASSWORD_PARAM] )){
            // user and password are correct
            updateWebsite();
        }else{
            // login incorrect
            echo '<div>Login error</div>';
        }
    }else{
        // present the login form
        echo 'Websu - website uploader v 0.0.0 <br>' . PHP_EOL;
        echo '<form action="" method="post">' . PHP_EOL;
        echo 'username <input type="text" id="'.Parameters::USER_PARAM.'" name="'.Parameters::USER_PARAM.'"><br>' . PHP_EOL;
        echo 'password <input type="password" id="'.Parameters::PASSWORD_PARAM.'" name="'.Parameters::PASSWORD_PARAM.'"><br>' . PHP_EOL;
        echo '<input type="submit" id="submit" name="submit" value="update" >' . PHP_EOL;
        echo '</form>' . PHP_EOL;
    }
}


main();

?>
</body>
</html>