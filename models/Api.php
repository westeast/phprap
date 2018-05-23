<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_api".
 *
 * @property int $id
 * @property int $version_id 版本id
 * @property int $module_id 模块id
 * @property string $title 接口名
 * @property string $method 请求方式
 * @property string $uri 接口地址
 * @property string $remark 接口简介
 * @property int $status 接口状态 10:正常 20:删除
 * @property int $sort 接口排序
 * @property int $creater_id 创建者id
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Api extends Model
{

    const ACTIVE_STATUS  = 10;
    const DELETED_STATUS = 20;

    /**
     * 绑定数据表
     */
    public static function tableName()
    {
        return '{{%api}}';
    }

    /**
     * 获取创建者
     * @return \yii\db\ActiveQuery
     */
    public function getCreater()
    {
        return $this->hasOne(User::className(),['id'=>'creater_id']);
    }

    /**
     * 获取所属版本
     * @return \yii\db\ActiveQuery
     */
    public function getVersion()
    {
        return $this->hasOne(User::className(),['id'=>'version_id']);
    }

    /**
     * 获取所属模块
     * @return \yii\db\ActiveQuery
     */
    public function getModule()
    {
        return $this->hasOne(Module::className(),['id'=>'module_id']);
    }
}
