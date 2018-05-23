<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_module".
 *
 * @property int $id
 * @property int $project_id 项目id
 * @property int $version_id 版本id
 * @property int $creater_id 创建者id
 * @property string $title 模块名称
 * @property string $remark 项目描述
 * @property int $status 模块状态 10:正常 20:删除
 * @property int $sort 排序
 * @property string $created_at
 * @property string $updated_at
 */
class Module extends Model
{

    const ACTIVE_STATUS  = 10;
    const DELETED_STATUS = 20;

    /**
     * 绑定数据表
     */
    public static function tableName()
    {
        return '{{%module}}';
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
     * 获取所属项目
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(),['id'=>'project_id']);
    }

    /**
     * 获取所属版本
     * @return \yii\db\ActiveQuery
     */
    public function getVersion()
    {
        return $this->hasOne(Version::className(),['id'=>'version_id']);
    }

    /**
     * 获取模块接口
     * @return \yii\db\ActiveQuery
     */
    public function getApis()
    {
        $filter = [
            'status' => Api::ACTIVE_STATUS
        ];

        $sort = [
            'sort' => SORT_DESC,
            'id'   => SORT_DESC
        ];

        return $this->hasMany(Api::className(), ['module_id' => 'id'])->where($filter)->orderBy($sort);
    }

}
