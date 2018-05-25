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

    /**
     * 获取模型更新内容
     * @param $oldAttributes
     * @param $dirtyAttributes
     * @return string
     */
    public function getUpdateContent($oldAttributes, $dirtyAttributes)
    {

        $content = '';

        foreach ($dirtyAttributes as $name => $value) {

            $label = '<strong>' . $this->getAttributeLabel($name) . '</strong>';

            $oldValue = '<code>' . $oldAttributes[$name] . '</code>';
            $newValue = '<code>' . $value . '</code>';

            $oldAttributes[$name] && $content .= '将 ' . $label . ' 从' . $oldValue . '更新为' . $newValue . ',';
        }

        return trim($content, ',');
    }

}
