<?php
namespace Common;
/**
 +------------------------------------------------------------------------------
 * Cookie管理类
 +------------------------------------------------------------------------------
 * @category   Think
 * @package  Think
 * @subpackage  Util
 * @author    liu21st <liu21st@gmail.com>
 * @version   $Id: Cookie.class.php 2601 2012-01-15 04:59:14Z liu21st $
 +------------------------------------------------------------------------------
 */
class Cookie {
    // 判断Cookie是否存在
    static function is_set($name) {
        return isset($_COOKIE[C('COOKIE_PREFIX').$name]);
    }

    // 获取某个Cookie值
    static function get($name) {
        $value   = $_COOKIE[C('COOKIE_PREFIX').$name];
        $value   =  unserialize(base64_decode($value));
        return $value;
    }

    // 设置某个Cookie值
    static function set($name,$value,$expire='',$path='',$domain='') {
        if($expire=='') {
            $expire =   C('COOKIE_EXPIRE');
        }
        if(empty($path)) {
            $path = C('COOKIE_PATH');
        }
        if(empty($domain)) {
            $domain =   C('COOKIE_DOMAIN');
        }
        $expire =   !empty($expire)?    time()+$expire   :  0;
        $value   =  base64_encode(serialize($value));
        setcookie(C('COOKIE_PREFIX').$name, $value,$expire,$path,$domain);
        $_COOKIE[C('COOKIE_PREFIX').$name]  =   $value;
    }

    // 删除某个Cookie值
    static function delete($name) {
        Cookie::set($name,'',-3600);
        unset($_COOKIE[C('COOKIE_PREFIX').$name]);
    }

    // 清空Cookie值
    static function clear() {
        unset($_COOKIE);
    }
}