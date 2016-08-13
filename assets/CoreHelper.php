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
        return (self::isGuest()) ? TRUE : FALSE;
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
     * @param            $min
     * @param            $max
     * @param bool|FALSE $leading_zero
     *
     * @return array
     */
    public static function minMaxRange($min, $max, $leading_zero = FALSE)
    {
        $array = [];
        for ($i = $min; $i <= $max; $i++) {
            $array[$i] = ($leading_zero && $i < 10) ? "0" . $i : $i;
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
        $url = preg_replace('/[^a-z|0-9|\-|\.|\+]/', '-', strtolower(trim($url)));
        return preg_replace('/-+/','-', $url);
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
        $array = ['random_number', 'random_uppercase', 'random_lowercase'];
        $token = $leading_symbol;

        while (strlen($token) < $length + 1) {
            for ($ii = 0; $ii < 5; $ii++) {
                $call = (int)rand(0, 2);
                $token .= self::$array[$call]();
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

        $array = [];
        if (!is_dir($path)) {
            die("No Directory: " . $path);
        }
        $items = scandir($path);
        foreach ($items as $item) {
            if ($item != "." && $item != "..") {
                if (is_file($path . "/" . $item)) {
                    $array[]['item'] = [
                        'is_dir'    => FALSE,
                        'path'      => $path,
                        'relative'  => str_replace($base_path, '', $path),
                        'file'      => $item,
                        'extension' => self::fileExtension($item),
                    ];
                }
            }
        }
        foreach ($items as $item) {
            if ($item != "." && $item != "..") {
                if (is_dir($path . "/" . $item)) {
                    $array[]['item'] = [
                        'is_dir'      => TRUE,
                        'path'        => $path,
                        'relative'    => str_replace($base_path, '', $path),
                        'folder'      => $item,
                        'depth'       => self::folderCountInPath(str_replace($base_path, '', $path . "/" . $item)),
                        'sub_folders' => self::recursiveDirectory($path . "/" . $item, $base_path),
                    ];
                }
            }
        }

        return $array;
    }

    /**
     * @param $file_name
     *
     * @return mixed
     */
    static public function fileExtension($file_name)
    {

        $f = explode('.', $file_name);

        return strtolower($f[sizeof($f) - 1]);
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
     * @param            $path
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
     * @param $path
     */
    static public function buildPath($path)
    {
        $dirs = '';
        $base_path = self::getBasePath();
        $path = self::cleanSlashInPath($path);
        $path = str_replace($base_path, '', $path);
        foreach (explode('/', $path) as $dir) {
            if (!$dir) {
                continue;
            }
            $dirs .= '/' . $dir;
            if (!is_dir($base_path . $dirs)) {
                mkdir($base_path . $dirs);
            }
        }

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
     * @param        $date
     * @param string $format
     *
     * @return int
     */
    static public function dateToTime($date, $format = 'YYYY-MM-DD')
    {
        $date = trim($date);
        $month = $day = $year = 0;
        if ($format == 'YYYY-MM-DD') {
            list($year, $month, $day) = explode('-', $date);
        }
        if ($format == 'YYYY/MM/DD') {
            list($year, $month, $day) = explode('/', $date);
        }
        if ($format == 'YYYY.MM.DD') {
            list($year, $month, $day) = explode('.', $date);
        }
        if ($format == 'DD-MM-YYYY') {
            list($day, $month, $year) = explode('-', $date);
        }
        if ($format == 'DD/MM/YYYY') {
            list($day, $month, $year) = explode('/', $date);
        }
        if ($format == 'DD.MM.YYYY') {
            list($day, $month, $year) = explode('.', $date);
        }
        if ($format == 'MM-DD-YYYY') {
            list($month, $day, $year) = explode('-', $date);
        }
        if ($format == 'MM/DD/YYYY') {
            list($month, $day, $year) = explode('/', $date);
        }
        if ($format == 'MM.DD.YYYY') {
            list($month, $day, $year) = explode('.', $date);
        }

        return (int) mktime(0, 0, 0, $month, $day, $year);

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

    /**
     * @param     $x_over
     * @param     $y
     * @param int $decimal_places
     * @return string
     */
    static public function getPercentage($x_over, $y, $decimal_places = 0)
    {
        return number_format(100 - ($x_over / $y) * 100, $decimal_places);
    }


    /**
     * @param $array_in
     *
     * @return array
     */
    public static function objectToArray($array_in)
    {
        $array = [];
        if (is_object($array_in)) {
            return self::objectToArray(get_object_vars($array_in));
        } else {
            if (is_array($array_in)) {
                foreach ($array_in as $key => $value) {
                    if (is_object($value)) {
                        $array[$key] = self::objectToArray($value);
                    } elseif (is_array($value)) {
                        $array[$key] = self::objectToArray($value);
                    } else {
                        $array[$key] = $value;
                    }
                }
            }
        }

        return $array;
    }


    /**
     * @param $name
     *
     * @return string
     */
    static public function NameToClass($name)
    {
        $name = strtolower(trim($name));
        $name = preg_replace('/[\s\t]+/', ' ', $name);
        $name = preg_replace('/_/', ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);

        return str_replace(' ', '', ucwords($name));
    }


    /**
     * @return array
     */
    static public function monthsArray()
    {
        $months = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
        $array = [];
        foreach ($months as $k => $v) {
            $array[$k + 1] = ($k + 1) . ' - ' . $v;
        }

        return $array;
    }


    /**
     * @param $model_class
     * @param $array
     *
     * @return bool|\yii\db\ActiveRecord
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    static public function saveModelForm($model_class, $array)
    {

        /** @var  $model \yii\db\ActiveRecord */
        $model = new $model_class();

        if (isset($array['id']) && $array['id']) {
            $model->setIsNewRecord(FALSE);
        }

        foreach ($array as $k => $v) {
            $model[$k] = $v;
        }

        if ($model->isNewRecord && $model->validate() && $model->save()) {
            return $model;
        }
        if (!$model->isNewRecord) {
            $sql = "UPDATE " . $model->getTableSchema()->fullName . " ";
            $sql .= "SET ";
            foreach ($array as $k => $v) {
                if ($k == "id") {
                    continue;
                }
                $sql .= "`" . strtolower(trim($k)) . "` = '" . addslashes(trim($v)) . "',";
            }
            $sql = rtrim($sql, ',');
            $sql .= " WHERE `id` = " . $array['id'];
            Yii::$app->db->createCommand($sql)->execute();

            return $model;
        }

        echo PHP_EOL . $model_class . "<BR>" . PHP_EOL;
        print_r($model->getErrors());
        exit;

        Alerts::setMessage('Model: ' . $model_class . '<br>Error: ' . print_r($model->getErrors(), TRUE));
        Alerts::setAlertType(Alerts::ALERT_DANGER);

        return FALSE;
    }

    /**
     * @param $css
     * @return string
     */
    static public function cleanCss($css)
    {
        $out = '';
        $max = 0;
        $min = 20;
        $array = [];
        $index = -1;
        foreach (explode(PHP_EOL, $css) as $line) {
            $css = $line;


            if (stripos($css, ':') == FALSE) {
                $array[] = $line;
            } else {

                list($a, $b) = explode(':', $css);
                $a = trim($a);
                $b = trim($b);
                if ($max < strlen($a)) {
                    $max = strlen($a);
                }
                if ($min > strlen($b)) {
                    $min = strlen($b);
                }
                $array[] = [$a, $b];
            }
        }

        foreach ($array as $item) {
            if (is_array($item) == FALSE) {
                $out .= $item;
                continue;
            }
            $len = $max - strlen($item[0]);
            $ratio = ceil($max / $min / 2);
            $ratio = ($ratio < 3) ? 3 : $ratio;
            $ratio = ($ratio > 3) ? 4 : $ratio;
            if (strlen($item[0]) == $max) {
                $out .= $item[0] . "\t: " . trim($item[1]) . PHP_EOL;
            } else {
                $out .= $item[0] . str_repeat("\t", ceil(($len + 1) / $ratio)) . ': ' . trim($item[1]) . PHP_EOL;
            }

        }

        return $out;
    }

}