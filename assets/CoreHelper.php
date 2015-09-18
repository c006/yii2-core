<?php

namespace c006\core\assets;

use Yii;

/**
 * Class CoreHelper
 * @package c006\core\assets
 */
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

    /**
     * @param $url
     *
     * @return string
     */
    static public function formatUrl($url)
    {
        return '';
    }


    /**
     * @param int $length
     * @param bool|TRUE $use_dash
     * @param string $leading_symbol
     *
     * @return string
     */
    static public function createToken($length = 32, $use_dash = TRUE, $leading_symbol = '')
    {
        $array = array('random_number', 'random_uppercase', 'random_lowercase');
        $token = $leading_symbol;

        while (strlen($token) < $length + 1) {
            for ($ii = 0; $ii < 5; $ii++) {
                $call = (int)rand(0, 2);
                $token .= self::$array[ $call ]();
            }
            $token .= ($use_dash) ? '-' : '';
        }

        return rtrim($token, '-');
    }

    /**
     * @param int $n
     *
     * @return string
     */
    static public function random_number($n = 1)
    {
        $out = '';
        for ($i = 0; $i < $n; $i++) {
            $out .= rand(0, 9);
        }

        return $out;
    }

    /**
     * @param int $n
     *
     * @return string
     */
    static public function random_uppercase($n = 1)
    {
        $out = '';
        for ($i = 0; $i < $n; $i++) {
            $out .= chr(rand(65, 90));
        }

        return $out;
    }


    /**
     * @param int $n
     *
     * @return string
     */
    static public function random_lowercase($n = 1)
    {
        $out = '';
        for ($i = 0; $i < $n; $i++) {
            $out .= chr(rand(97, 122));
        }

        return $out;
    }

    /**
     * @param $path
     * @param $base_path
     *
     * @return array
     */
    static public function recursiveDirectory($path, $base_path)
    {

        $array = array();
        if (!is_dir($path))
            die("No Directory: " . $path);
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item != "." && $item != "..") {
                if (is_file($path . "/" . $item)) {
                    $array[]['item'] = array(
                        'is_dir'    => FALSE,
                        'path'      => $path,
                        'relative'  => str_replace($base_path, '', $path),
                        'file'      => $item,
                        'extension' => self::fileExtension($item),
                    );
                }
            }
        }
        foreach ($items as $item) {
            if ($item != "." && $item != "..") {
                if (is_dir($path . "/" . $item)) {
                    $array[]['item'] = array(
                        'is_dir'      => TRUE,
                        'path'        => $path,
                        'relative'    => str_replace($base_path, '', $path),
                        'folder'      => $item,
                        'depth'       => self::folderCountInPath(str_replace($base_path, '', $path . "/" . $item)),
                        'sub_folders' => self::recursiveDirectory($path . "/" . $item, $base_path),
                    );
                }
            }
        }

        return $array;
    }

    /**
     * @param $path
     *
     * @return int
     */
    static public function folderCountInPath($path)
    {

        $path = self::cleanSlashInPath($path);
        $path = self::removeTrailingBackSlash($path);

        return sizeof(explode('/', $path));
    }

    /**
     * @param $file_name
     *
     * @return mixed
     */
    static public function  fileExtension($file_name)
    {

        $f = explode('.', $file_name);

        return strtolower($f[ sizeof($f) - 1 ]);
    }

    /**
     * @param $path
     * @param bool|FALSE $is_forward
     *
     * @return mixed
     */
    static public function cleanSlashInPath($path, $is_forward = FALSE)
    {

        if ($is_forward) {
            return str_replace('/', '\\', $path);
        }

        return str_replace('\\', '/', $path);
    }


    /**
     * @param $path
     *
     * @return string
     */
    static public function removeTrailingBackSlash($path)
    {

        if (substr($path, strlen($path) - 1, 1) == "/") {
            return substr($path, 0, strlen($path) - 1);
        }

        return $path;
    }

    /**
     * @return mixed
     */
    static public function getBasePath()
    {
        $path = self::cleanSlashInPath(Yii::$app->basePath);
        $path = str_replace('protected', '', $path);

        return preg_replace('/\/$/', '', $path);
    }

    /**
     * @param $path
     */
    static public function buildPath($path)
    {
        $dirs = '';
        $base_path = self::getBasePath();
        $path = self::cleanSlashInPath($path);
        $path = str_replace($base_path, '', $path);
        foreach (explode('/', $path) as $dir) {
            if (!$dir)
                continue;
            $dirs .= '/' . $dir;
            if (!is_dir($base_path . $dirs))
                mkdir($base_path . $dirs);
        }

    }

    /**
     * @param $date
     * @param string $format
     *
     * @return int
     */
    static public function dateToTime($date, $format = 'YYYY-MM-DD')
    {
        $month = $day = $year = 0;
        if ($format == 'YYYY-MM-DD') list($year, $month, $day) = explode('-', $date);
        if ($format == 'YYYY/MM/DD') list($year, $month, $day) = explode('/', $date);
        if ($format == 'YYYY.MM.DD') list($year, $month, $day) = explode('.', $date);

        if ($format == 'DD-MM-YYYY') list($day, $month, $year) = explode('-', $date);
        if ($format == 'DD/MM/YYYY') list($day, $month, $year) = explode('/', $date);
        if ($format == 'DD.MM.YYYY') list($day, $month, $year) = explode('.', $date);

        if ($format == 'MM-DD-YYYY') list($month, $day, $year) = explode('-', $date);
        if ($format == 'MM/DD/YYYY') list($month, $day, $year) = explode('/', $date);
        if ($format == 'MM.DD.YYYY') list($month, $day, $year) = explode('.', $date);

        return mktime(0, 0, 0, $month, $day, $year);

    }

    /**
     * @param $string
     *
     * @return bool
     */
    static public function hasUppercase($string)
    {
        return (bool)preg_match('/[A-Z]/', $string);
    }

}