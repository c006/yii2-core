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

}