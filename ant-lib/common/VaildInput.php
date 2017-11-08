<?php

/**
 * Created by PhpStorm.
 * User: shenzhe
 * Desc: 字段验证
 */

namespace common;

use exceptionHandler\InputVaildException;

class VaildInput
{
    const EMAIL = 'e';              //邮件
    const MOBILE = 'm';             //手机号
    const URL = 'u';                //网址
    const INT = 'i';                //整型数字
    const FLOAT = 'f';              //浮点数字
    const STRING = 's4-16';         //字符串,默认长度在 4~16 之间
    const NAME = 'n4-16';           //字母开头，只包含字母，数字 -_ 默认长度在 4~16 之间
    const CHINESE = 'c2-4';         //中文，默认长度在 2~4 之间
    const IP = 'p';                 //ip地址
    const MAC = 'a';                //mac地址

    /**
     * @param $str
     * @param $type
     * @param string $errmsg
     * @return mixed
     * @throws InputVaildException
     */
    public static function vaild($str, $type, $errmsg = '')
    {
        if (empty($type)) {
            throw new InputVaildException('type empty');
        }
        switch (substr($type, 0, 1)) {
            case 'e':  //电子邮件
                return self::email($str, $errmsg);
                break;
            case 'm':  //手机号
                return self::mobile($str, $errmsg);
                break;
            case 'u': //网址
                return self::url($str, $errmsg);
                break;
            case 'i':   //整形
                if (strlen($type) > 1) {
                    $filter = explode('-', substr($type, 1));
                } else {
                    $filter = [0, 0];
                }
                return self::int($str, intval($filter[0]), intval($filter[1]), $errmsg);
                break;
            case 'f':   //浮点数字
                if (strlen($type) > 1) {
                    $filter = explode('-', substr($type, 1));
                } else {
                    $filter = [0, 0];
                }
                return self::float($str, intval($filter[0]), intval($filter[1]), $errmsg);
                break;
            case 's':   //字符串 smin-max
                if (strlen($type) > 1) {
                    $filter = explode('-', substr($type, 1));
                } else {
                    $filter = [0, 0];
                }
                return self::string($str, intval($filter[0]), intval($filter[1]), $errmsg);
                break;
            case 'n':   //字母开头，只包含字母，数字 -_
                if (strlen($type) > 1) {
                    $filter = explode('-', substr($type, 1));
                } else {
                    $filter = [0, 0];
                }
                return self::username($str, intval($filter[0]), intval($filter[1]), $errmsg);
                break;
            case 'c':   //只匹配中文
                $filter = explode('-', substr($type, 1));
                return self::chinese($str, intval($filter[0]), intval($filter[1]), $errmsg);
                break;
            case 'g': //包含中文
                return self::hasChinese($str, $errmsg);
                break;
            case 'p':  //匹配ip
                return self::ip($str, $errmsg);
                break;
            case 'a':   //匹配mac地址
                return self::mac($str, $errmsg);
                break;
            default:   //默认按正则
                return self::regexp($str, $type, $errmsg);
                break;
        }
    }

    /**
     * @param $email
     * @param string $errmsg
     * @return mixed
     * @throws InputVaildException
     */
    public static function email($email, $errmsg = '')
    {
        $ret = filter_var($email, FILTER_VALIDATE_EMAIL);
        if (false === $ret) {
            $errmsg = $errmsg ? $errmsg : '错误的email格式';
            throw new InputVaildException($errmsg);
        }

        return $ret;
    }

    /**
     * @param $url
     * @param string $errmsg
     * @return mixed
     * @throws InputVaildException
     */
    public static function url($url, $errmsg = '')
    {
        $ret = filter_var($url, FILTER_VALIDATE_URL);
        if (false === $ret) {
            $errmsg = $errmsg ? $errmsg : '错误的url格式';
            throw new InputVaildException($errmsg);
        }

        return $ret;
    }

    /**
     * @param $ip
     * @param string $errmsg
     * @return mixed
     * @throws InputVaildException
     */
    public static function ip($ip, $errmsg = '')
    {
        $ret = filter_var($ip, FILTER_VALIDATE_IP);
        if (false === $ret) {
            $errmsg = $errmsg ? $errmsg : '错误的ip格式';
            throw new InputVaildException($errmsg);
        }

        return $ret;
    }

    /**
     * @param $int
     * @param int $min
     * @param int $max
     * @param string $errmsg
     * @return mixed
     * @throws InputVaildException
     */
    public static function int($int, $min = 0, $max = 0, $errmsg = '')
    {
        $options = [];
        if ($min) {
            $options['min_range'] = $min;
        }
        if ($max) {
            $options['min_range'] = $max;
        }
        $ret = filter_var($int, FILTER_VALIDATE_INT, [
            'options' => $options
        ]);
        if (false === $ret) {
            $errmsg = $errmsg ? $errmsg : '错误的数字格式';
            throw new InputVaildException($errmsg);
        }

        return $ret;
    }

    /**
     * @param $float
     * @param int $min
     * @param int $max
     * @param string $errmsg
     * @return mixed
     * @throws InputVaildException
     */
    public static function float($float, $min = 0, $max = 0, $errmsg = '')
    {
        $options = [];
        if ($min) {
            $options['min_range'] = $min;
        }
        if ($max) {
            $options['min_range'] = $max;
        }
        $ret = filter_var($float, FILTER_VALIDATE_FLOAT, [
            'options' => $options
        ]);
        if (false === $ret) {
            $errmsg = $errmsg ? $errmsg : '错误的数字格式';
            throw new InputVaildException($errmsg);
        }

        return $ret;
    }

    /**
     * @param $str
     * @param $regexp
     * @param string $errmsg
     * @return mixed
     * @throws InputVaildException
     */
    public static function regexp($str, $regexp, $errmsg = '')
    {
        $ret = filter_var($str, FILTER_VALIDATE_REGEXP, [
            'options' => [
                'regexp' => $regexp
            ]
        ]);
        if (false === $ret) {
            $errmsg = $errmsg ? $errmsg : '错误的格式';
            throw new InputVaildException($errmsg);
        }
        return $ret;
    }

    /**
     * @param $str
     * @param string $errmsg
     * @return mixed
     * @throws InputVaildException
     */
    public static function mac($str, $errmsg = '')
    {
        $ret = filter_var($str, FILTER_VALIDATE_MAC);
        if (false === $ret) {
            $errmsg = $errmsg ? $errmsg : '错误mac地址的格式';
            throw new InputVaildException($errmsg);
        }
        return $ret;
    }

    /**
     * @param $mobileNum
     * @param string $errmsg
     * @return mixed
     * @throws InputVaildException
     */
    public static function mobile($mobileNum, $errmsg = '')
    {
        $errmsg = $errmsg ? $errmsg : '错误的手机号码';
        return self::regexp($mobileNum, '/1{1}\d{10}$/', $errmsg);
    }

    ///^[a-zA-Z][a-zA-Z0-9-_]{3,16}$/
    public static function username($str, $min = 1, $max = 50, $errmsg = '')
    {
        $errmsg = $errmsg ? $errmsg : "必需以字母开头且长度在{$min}-{$max}之间";
        return self::regexp($str, '/^[a-zA-Z][a-zA-Z0-9-_\-]{' . $min . ',' . $max . '}$/', $errmsg);
    }

    /**
     * @param $str
     * @param int $min
     * @param int $max
     * @param string $errmsg
     * @return mixed
     * @throws InputVaildException
     */
    public static function string($str, $min = 1, $max = 50, $errmsg = '')
    {
        $ret = filter_var($str, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW | FILTER_FLAG_ENCODE_AMP);
        if ($ret == false) {
            $errmsg = $errmsg ? $errmsg : '错误输入格式';
            throw new InputVaildException($errmsg);
        }

        if ($min && strlen($ret) < $min) {
            throw new InputVaildException('至少' . $min . '个字符');
        }

        if ($max && strlen($ret) > $max) {
            throw new InputVaildException('至多' . $max . '个字符');
        }

        return $ret;
    }

    /**
     * @param $str
     * @param int $min
     * @param int $max
     * @param $errmsg
     * @return mixed
     * @throws InputVaildException
     */
    public static function chinese($str, $min = 1, $max = 50, $errmsg)
    {
        $errmsg = $errmsg ? $errmsg : '必需为中文';
        $ret = self::regexp($str, '/^[\x{4e00}-\x{9fa5}]+$/u', $errmsg);
        if ($min && mb_strlen($ret) < $min) {
            $errmsg = '至少' . $min . '个汉字';
            throw new InputVaildException($errmsg);
        }

        if ($max && mb_strlen($ret) > $max) {
            $errmsg = '至多' . $max . '个汉字';
            throw new InputVaildException($errmsg);
        }

        return $ret;
    }

    /**
     * @param $str
     * @param string $errmsg
     * @return mixed
     * @throws InputVaildException
     */
    public static function hasChinese($str, $errmsg = '')
    {
        $errmsg = $errmsg ? $errmsg : '必需包含为中文';
        return self::regexp($str, '/[^\x00-\x80]/', $errmsg);
    }
}