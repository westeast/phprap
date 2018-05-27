<?php

if (!function_exists('url')){
    function url($route = '', $param = [], $scheme = false)
    {

        if(!$route){
            return \yii\helpers\Url::current();
        }

        if(is_array($param) && $param){
            $param[] = $route;
        }else{
            $param = $route;
        }

        return  \yii\helpers\Url::toRoute($param, $scheme);

    }
}

/**
 * 友好的打印调试
 */
if (!function_exists('dump'))
{

    function dump()
    {

        if(func_num_args() < 1){

            var_dump(null);

        }

        //获取参数列表
        $args_list = func_get_args();

        echo '<pre>';

        foreach ($args_list as $arg) {

            $type = gettype($arg);

            if(!$arg){

                var_dump($arg);

            }elseif($type == 'array'){

                print_r($arg);

            }elseif(in_array($type, ['object', 'resource', 'boolean', 'NULL', 'unknown type'])){

                var_dump($arg);

            }else{

                echo $arg . '<br>';

            }

        }

        echo "</pre>";

    }

}

/**
 * 获取系统配置信息
 */
if (!function_exists('config')){
    function config($name, $type='app')
    {
        $name  = trim($name);

        return \app\models\Config::findModel(['type' => $type])->getField($name);

    }
}

/**
 * 生成CSRF口令
 */
if (!function_exists('csrf_token')){
    function csrf_token()
    {

        return Yii::$app->request->csrfToken;

    }
}

/**
 * 字符串转数字
 * @param $str
 * @return string
 */
function str2num($str) {

    preg_match_all('/([a-z]+)|([0-9]+)|([^0-9a-z]+)/i', $str, $data);

    foreach($data[0] as $v) {
        foreach(str_split($v, 1) as $v1)
            $data1[] = 999 - ord($v1);
    }

    return implode('', $data1);
}

/**
 * 数字转字符串
 * @param $num
 * @return string
 */
function num2str($num) {

    $str = '';

    foreach(str_split($num, 3) as $v) {
        $str .= chr(999 - $v);
    }

    return $str;
}

/**
 * 生成uuid
 * @param string $prefix
 * @return string
 */
if (!function_exists('uuid')){
    function uuid($prefix = ""){

        $str = uniqid(mt_rand(), true);
        $uuid  = substr($str,0,8) . '-';
        $uuid .= substr($str,8,4) . '-';
        $uuid .= substr($str,12,4) . '-';
        $uuid .= substr($str,16,4) . '-';
        $uuid .= substr($str,20,12);
        return $prefix . $uuid;

    }
}




