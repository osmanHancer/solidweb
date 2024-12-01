<?php
class LH
{
    public static $dict = [];
    public static $lid = '';
    public static $cacheTimeout = 3600;

    public static function t($key) //transform function for view or actions 
    {
        if (isset(LH::$dict[$key]))
            return LH::$dict[$key];
        else {
            LH::missKey($key);
            return  $key;
        }
    }


    public static function langLoad($code)
    {

       $lang= dbSqliteC::getDB()->one("SELECT * FROM langs WHERE lang=:lang", ["lang" => $code]);
        LH::$dict = dbSqliteC::getDB()->getDict(
            "SELECT * FROM lang_keys lk
            LEFT JOIN lang_data ld ON lk.kid=ld.kid AND ld.lid=:lid 
             WHERE ld.lid=:lid",
            "lkey",
            "lval",
            ["lid" => $lang["lid"]]
        );
       // var_dump(LH::$dict);
    }


    public static function langCheck($code)
    {
        LH::$lid = Q_APP::escapeDir($code);
        if (!empty(LH::$dict)) return;  //dont touch next reload.

        LH::langLoad($code);

        // $cachetime = LH::g('lang_' . LH::$code. '_time');
        // if (time() - $cachetime <= LH::$cacheTimeout) {
        //     LH::$dict =  LH::g('lang_' . LH::$lid . '_data');
        //     if (LH::$dict && !empty(LH::$dict)) return;
        // }



        // if (!$cachetime || $changeTime > $cachetime || time() - $cachetime > LH::$cacheTimeout) {
        //     LH::langLoad($langFile);
        //     LH::s('lang_' . $lid . '_data', LH::$dict);
        //     LH::s('lang_' . $lid . '_time', time());
        // }
    }

    public static function missKey($key)
    {
        if (!defined('DEV_MODE'))
            return;
        // $line = time() . '|'.$key . '|' . $_SERVER['REQUEST_URI'] . "\n";
        // file_put_contents(LANG_FOLDER . LH::$lid . '_.bak', $line,FILE_APPEND);
    }

    public static function langList()
    {

        return [];
    }

    public static function s($key, $val, $ttl = 3600) //memory store write
    {
        return call_user_func_array('apcu_store', [$key, $val, $ttl]);
    }

    public static function g($key) //memory store get
    {
        return call_user_func_array('apcu_fetch', [$key]);
    }
}
