<?php
/*
 * Check this stack overflow answer for a very easy in depth of encryption with openSSH with PHP
 * https://stackoverflow.com/a/10945097/20140066
 */

/**
 * @throws SodiumException
 * @throws Exception
 *
 */

function get_key_for_encryption()
{
    $key_size = 32; // 256 bits
    $encryption_key = openssl_random_pseudo_bytes($key_size, $strong);
    // $strong will be true if the key is crypto safe
    return $encryption_key;
}

function get_iv_for_encryption(){
    $iv_size = 16; // 128 bits
    $iv = openssl_random_pseudo_bytes($iv_size, $strong);
    return $iv;
}

function pkcs7_pad($data, $size)
{
    $length = $size - strlen($data) % $size;
    return $data . str_repeat(chr($length), $length);
}


function pkcs7_unpad($data)
{
    return substr($data, 0, -ord($data[strlen($data) - 1]));
}


function encrypt_data($plain_text_data, $key, $iv){
    $cipher_text = openssl_encrypt(
        pkcs7_pad($plain_text_data, 16), // padded data
        'AES-256-CBC',        // cipher and mode
        $key,      // secret key
        0,                    // options (not used)
        $iv                   // initialisation vector
    );
    return $cipher_text;

}

function decrypt_data($cipher_data, $key, $iv){
    $plain_text = pkcs7_unpad(openssl_decrypt(
        $cipher_data,
        'AES-256-CBC',
        $key,
        0,
        $iv
    ));
    return $plain_text;
}


//if ($argv && $argv[0] && realpath($argv[0]) === __FILE__) {
//    echo "HIIII\n";
//    $mes = "ajsrsgfvaiwuch3478g2c8129c4y9234c789104cn578329v623623623t23623461578cynrtbyvbr@v54t4ye45x5978034ghs";
//    $priv_key = get_key_for_encryption();
//    $iv = get_iv_for_encryption();
//    $secret_mes = encrypt_data($mes, $priv_key, $iv);
//
//
//    $plain_mes = decrypt_data($secret_mes, $priv_key, $iv);
//    $cmes = strlen($mes);
//    $ccmes = strlen($secret_mes);
//    echo "$secret_mes : $ccmes\n";
//    echo "$plain_mes : $cmes\n";
//    echo "$mes\n";
//    echo "$iv\n";
//    echo strlen($iv);
//    echo gettype($iv);
//
//}



?>

