<?php

namespace app\models;

use Yii;

class Model extends \yii\db\ActiveRecord
{

    public $model;
    public $models;
    public $pages;
    public $params;
    public $count;
    public $sql;
    public $pageSize =20;

    public static function findModel($condition = null)
    {

        if (($model = static::findOne($condition)) !== null) {

            return $model;

        } else {

            return new static();
        }
    }

    /**
     * 获取错误字段
     * @return int|null|string
     */
    public function getLabel()
    {
        return key($this->getFirstErrors());
    }

    /**
     * 获取错误信息
     * @return mixed
     */
    public function getError()
    {

        return current($this->getFirstErrors());

    }

}
