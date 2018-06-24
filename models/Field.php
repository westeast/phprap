<?php

namespace app\models;

use Yii;

class Field extends Model
{

    /**
     * 字段类型标签
     * @var array
     */
    public $typeLabels = [
        'string' => '字符串(string)',
        'integer' => '整数(integer)',
        'float'   => '小数(float)',
        'boolean' => '布尔(boolean)',
        'object'  => '对象(object)',
        'array'   => '数组(array)',
    ];

    /**
     * 是否必须标签
     * @var array
     */
    public $requiredLabels = [
        '10' => '是',
        '20' => '否',
    ];

    /**
     * 判断字段是否是符合类型
     * @param $field
     * @return bool
     */
    public function isComplexType($type)
    {
        return in_array($type, ['array', 'object']) ? true : false;

    }

}
