<?php

namespace set\components\util;

class CryptoAes
{
    static public function encrypt($value, $passphrase)
    {
        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';
		for($i = strlen($salted); $i < 48; $i += 16)
		{
			$dx = md5($dx.$passphrase.$salt, true);
			$salted .= $dx;
		}
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $data = array('ct' => base64_encode($encrypted_data), 'iv' => bin2hex($iv), 's' => bin2hex($salt));
        return json_encode($data);
    }

    static public function decrypt($jsonStr, $passphrase)
    {
        $json = json_decode($jsonStr, true);
        $salt = hex2bin($json['s']);
        $iv = hex2bin($json['iv']);
        $ct = base64_decode($json['ct']);
        $concatedPassphrase = $passphrase.$salt;
        $md5 = array();
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        $i = 1;
        for($i = count($md5); $i < 32; $i++)
		{
			$md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
			$result .= $md5[$i];
		}
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);
        return json_decode($data, true);
    }
}

?>