<?php

$key='SADFo92jzVnzSj39IUYGvi6eL8v6RvJH8Cytuiouh547vCytdyUFl76R';
$data='ciao ciao ciao';
echo "data: " . $data;

$ivSize = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_CBC);
$iv = mcrypt_create_iv($ivSize, MCRYPT_DEV_URANDOM); 
$encrypted = mcrypt_encrypt(
    MCRYPT_BLOWFISH,
    $key,
    $data,
    MCRYPT_MODE_CBC,
    $iv
);
$macKey = mhash_keygen_s2k(MHASH_SHA256, $key, $iv, 32);
$hmac = hash_hmac('sha256', $iv . MCRYPT_BLOWFISH . $encrypted, $macKey);
$output = $hmac . ':' . base64_encode($iv) . ':' . base64_encode($encrypted);
echo "out: " . $output;

list($hmac, $iv, $encrypted)= explode(':',$output);
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
echo "data: " . $data;


?>