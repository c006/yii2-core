<?php

namespace c006\core\assets;

use Yii;

class CoreHelper
{

    /**
     * @return bool
     */
    static public function checkLogin()
    {
        return TRUE;
    }

    /**
     * @return bool
     */
    static public function isGuest()
    {
        return Yii::$app->user->isGuest;
    }

    /**
     * @param int $time
     *
     * @return bool|string
     */
    static public function mysqlTimestamp($time = 0)
    {
        $time = ($time) ? $time : time();

        return date('Y-m-d H:i:s', $time);
    }

    /**
     * @param $min
     * @param $max
     * @param bool|FALSE $leading_zero
     *
     * @return array
     */
    public static function minMaxRange($min, $max, $leading_zero = FALSE)
    {
        $array = array();
        for ($i = $min; $i <= $max; $i++) {
            $array[ $i ] = ($leading_zero && $i < 10) ? "0" . $i : $i;
        }

        return $array;
    }

    static public function formatUrl($url)
    {
        return '';
    }


    static public function createToken($length = 32)
    {
        $array = array('random_number', 'random_uppercase', 'random_lowercase');
        $token = '';

        while ($length) {
            for ($ii = 0; $ii < 5; $ii++) {
                $call = rand(0, 2);
                $token .= self::$array[ $call ]();
                $length--;
            }
            $token .= '-';
        }

        return rtrim($token, '-');
    }

    static public function random_number($n = 1)
    {
        $out = '';
        for ($i = 0; $i < $n; $i++) {
            $out .= rand(0, 9);
        }

        return $out;
    }

    static public function random_uppercase($n = 1)
    {
        $out = '';
        for ($i = 0; $i < $n; $i++) {
            $out .= chr(rand(65, 90));
        }

        return $out;
    }


    static public function random_lowercase($n = 1)
    {
        $out = '';
        for ($i = 0; $i < $n; $i++) {
            $out .= chr(rand(97, 122));
        }

        return $out;
    }

}