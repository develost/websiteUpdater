<?php
    function endsWith($haystack, $needle) {
        return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== FALSE);
    }
    function lReplace($string,$find,$replace){
        $result = preg_replace(strrev("/$find/"),strrev($replace),strrev($string),1);
        return strrev($result);
    }
    
    function encrypt($plainContents,$key){
        $ivSize = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_DEV_URANDOM); 
        $encrypted = mcrypt_encrypt(
            MCRYPT_BLOWFISH,
            $key,
            $plainContents,
            MCRYPT_MODE_CBC,
            $iv
        );
        $macKey = mhash_keygen_s2k(MHASH_SHA256, $key, $iv, 32);
        $hmac = hash_hmac('sha256', $iv . MCRYPT_BLOWFISH . $encrypted, $macKey);
        $output = $hmac . ':' . base64_encode($iv) . ':' . base64_encode($encrypted);
        return $output;
    }
    
    function decrypt($encryptedContent,$key){
        list($hmac, $iv, $encrypted)= explode(':',$encryptedContent);
        $iv = base64_decode($iv);
        $encrypted = base64_decode($encrypted);
        $macKey = mhash_keygen_s2k(MHASH_SHA256, $key, $iv, 32);
        $newHmac= hash_hmac('sha256', $iv . MCRYPT_BLOWFISH . $encrypted, $macKey);
        if ($hmac!==$newHmac) {
            die('Autenticazione fallita, impossibile procedere.');
        }
        $decrypt = mcrypt_decrypt(
            MCRYPT_BLOWFISH,
            $key,
            $encrypted,
            MCRYPT_MODE_CBC,
            $iv
        );
        $data = rtrim($decrypt, "\0");
        return $data;
    }
    
    function toBin($key,$basePath,$extensions,$binExtension){
        print "Start toBin!\n";
        $path = realpath($basePath);
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        foreach($objects as $filename => $object){
            foreach ($extensions as $extension) {
                if (endsWith($filename,'.'.$extension)){
                    $binName = $filename . '.' .$binExtension;
                    echo $filename . " --> " . $binName . "\n";
                    $handle = fopen($filename, "rb");
                    $plainContents = fread($handle, filesize($filename));
                    fclose($handle);
                    $encryptedContents = encrypt($plainContents,$key);
                    file_put_contents($binName, $encryptedContents);
                }
            }
        }
        print "End toBin!\n";
    }

    function fromBin($key,$basePath,$extensions,$binExtension){
        print "Start fromBin\n";
        $path = realpath($basePath);
        $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path), RecursiveIteratorIterator::SELF_FIRST);
        foreach($objects as $binName => $object){
            foreach ($extensions as $extension) {
                if (endsWith($binName,'.'.$extension.'.'.$binExtension)){
                    $filename = lReplace($binName,'.'.$binExtension,'');
                    echo $filename . " <-- " . $binName . "\n";
                    $handle = fopen($binName, "rb");
                    $encryptedContents = fread($handle, filesize($binName));
                    fclose($handle);
                    $plainContents = decrypt($encryptedContents,$key);
                    file_put_contents($filename, $plainContents);
                }
            }
        }
        print "End fromBin!\n"; 
    }
?>
<?php
    // ---------------------------------------------------------------
    // Main
    // ---------------------------------------------------------------
    parse_str(implode('&', array_slice($argv, 1)), $_GET);
    $mode = $_GET['mode'];
    $key = $_GET['key'];
    $basePath = $_GET['basePath'];
    $extensions = explode(" ", $_GET['extensions']);
    $binExtension = $_GET['binExtension'];
    
    print "mode: ".$mode."\n";
    print "key: ".$key."\n";
    print "basePath: ".$basePath."\n";
    print "Extensions: ";
    print_r ($extensions);
    print "binExtension: ".$binExtension."\n";
    
    if (0 == strcmp($mode,"all")){
        toBin($key,$basePath,$extensions,$binExtension);
        fromBin($key,$basePath,$extensions,$binExtension);
    }else if (0 == strcmp($mode,"to")){
        toBin($key,$basePath,$extensions,$binExtension);
    }else if (0 == strcmp($mode,"from")){
        fromBin($key,$basePath,$extensions,$binExtension);
    }else {
        print "Error: mode not found" . $mode ."\n";
    }
    
    print "--DONE--\n";
?>
