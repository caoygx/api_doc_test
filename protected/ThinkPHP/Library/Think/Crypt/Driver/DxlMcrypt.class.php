<?php
namespace Think\Crypt\Driver;
/**
 * Mcrypt_des加解密
 */
class DxlMcrypt {

    /**
     * DES 加密函数
     * @param string $plain_text
     * @param string $key
	 * @param string $iv
     */
	public static function encrypt($plain_text, $key = 'zb8964116sjts3dhe156ydsx', $iv = '1d3w6g8x') {
		if(empty($key)) $key = 'zb8964116sjts3dhe156ydsx';
		if(empty($iv)) $iv = '1d3w6g8x';
		$padded = self::pkcs5Pad ( $plain_text, mcrypt_get_block_size ( MCRYPT_TRIPLEDES, MCRYPT_MODE_CBC ) );
		return @base64_encode ( mcrypt_encrypt ( MCRYPT_TRIPLEDES, $key, $padded, MCRYPT_MODE_CBC, $iv ) );
	}
	
    /**
     * DES 解密函数
     * @param string $plain_text
     * @param string $key
	 * @param string $iv
     */
	public static function decrypt($cipher_text, $key = 'zb8964116sjts3dhe156ydsx', $iv = '1d3w6g8x') {
		if(empty($key)) $key = 'zb8964116sjts3dhe156ydsx';
		if(empty($iv)) $iv = '1d3w6g8x';
		$plain_text = @mcrypt_decrypt ( MCRYPT_TRIPLEDES, $key, base64_decode ( $cipher_text ), MCRYPT_MODE_CBC, $iv );
		return self::pkcs5Unpad ( $plain_text );
	}

	public static function genIvParameter() {
		return mcrypt_create_iv ( mcrypt_get_iv_size ( MCRYPT_TRIPLEDES, MCRYPT_MODE_CBC ), MCRYPT_RAND );
	}
	
	private static function pkcs5Pad($text, $blocksize) {
		$pad = $blocksize - (strlen ( $text ) % $blocksize);
		return $text . str_repeat ( chr ( $pad ), $pad );
	}
	
	private static function pkcs5Unpad($text) {
		$pad = ord ( $text {strlen ( $text ) - 1} );
		if ($pad > strlen ( $text ))
			return false;
		if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad)
			return false;
		return substr ( $text, 0, - 1 * $pad );
	}
	
}
