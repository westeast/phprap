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
    public $pageSize = 20;

    const ACTIVE_STATUS  = 10; //启用状态
    const DISABLE_STATUS = 20; //禁用状态
    const DELETED_STATUS = 30; //删除状态

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

    /**
     * 获取加密id
     * @return string
     */
    public function getEncodeId()
    {
        return mt_rand(1000, 9999) . date('His');
    }

    /**
     * 获取模型更新内容
     * @param $oldAttributes 原始属性
     * @param $dirtyAttributes 更新属性
     * @param string $preText 前缀文案
     * @return string
     */
    public function getUpdateContent($oldAttributes, $dirtyAttributes, $preText = '')
    {

        $content = '';

        foreach ($dirtyAttributes as $name => $value) {

            $label = '<strong>' . $this->getAttributeLabel($name) . '</strong>';

            if(isset($oldAttributes[$name])){
                $oldValue = '<code>' . $oldAttributes[$name] . '</code>';
                $newValue = '<code>' . $value . '</code>';

                $content .= $preText . ' ' . $label . ' 从' . $oldValue . '更新为' . $newValue . ',';
            }

        }

        return trim($content, ',');
    }

}
