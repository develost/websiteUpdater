<html>
<head>
<title>Uploader - for internal use only</title>
</head>
<body>
<?php
include_once "parameters.php";

function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
    return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
}

function updateWebsite(){
    if (file_exists (Constants::ZIP_TEMP_NAME)){
        unlink(Constants::ZIP_TEMP_NAME);
    }

    $now = date(Constants::DATE_FORMAT);
    // get remote file to local string
    $zipString = file_get_contents(Constants::ZIP_URL);
    // write string to local file
    file_put_contents(Constants::ZIP_TEMP_NAME,$zipString);
    $zip = zip_open(Constants::ZIP_TEMP_NAME);
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
    unlink(Constants::ZIP_TEMP_NAME);


    $htaccessContent = "";
    $htaccessContent.= "# ------------------------------------------------" . PHP_EOL;
    $htaccessContent.= "# Created on " . $now . PHP_EOL;
    $htaccessContent.= "# ------------------------------------------------" . PHP_EOL;
    $htaccessContent.= "<IfModule mod_rewrite.c>" . PHP_EOL;
    $htaccessContent.= "    RewriteEngine On" . PHP_EOL;
    $htaccessContent.= "    RewriteCond %{REQUEST_URI} ^/uploader.php" . PHP_EOL;
    $htaccessContent.= "    RewriteRule ^(.*)$ $1 [L]" . PHP_EOL;
    $htaccessContent.= "    RewriteCond %{REQUEST_URI} !^/" . $websiteRoot . PHP_EOL;
    $htaccessContent.= "    RewriteRule ^(.*)$ ". $websiteRoot ."$1 [L]" . PHP_EOL;
    $htaccessContent.= "</IfModule>";

    file_put_contents(Constants::HTACCESS_FILE,$htaccessContent);
}


function main(){
    if (isset($_POST[Constants::USER_PARAM]) &&  isset($_POST[Constants::PASSWORD_PARAM])){
        if ((Constants::USER == $_POST[Constants::USER_PARAM]) && (Constants::PASSWORD == $_POST[Constants::PASSWORD_PARAM] )){
            // user and password are correct
            updateWebsite();
        }else{
            // login incorrect
            echo '<div>Login error</div>';
        }
    }else{
        // present the login form
        echo '<form action="" method="post">' . PHP_EOL;
        echo 'username <input type="text" id="'.Constants::USER_PARAM.'" name="'.Constants::USER_PARAM.'"><br>' . PHP_EOL;
        echo 'password <input type="password" id="'.Constants::PASSWORD_PARAM.'" name="'.Constants::PASSWORD_PARAM.'"><br>' . PHP_EOL;
        echo '<input type="submit" id="submit" name="submit">' . PHP_EOL;
        echo '</form>' . PHP_EOL;
    }
}


main();

?>
</body>
</html>