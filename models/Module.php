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
 * @property int $status 模块状态
 * @property int $sort 排序数字
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Module extends Model
{

    const ACTIVE_STATUS  = 10; //启用状态
    const DISABLE_STATUS = 20; //禁用状态
    const DELETED_STATUS = 30; //删除状态

    /**
     * 绑定数据表
     */
    public static function tableName()
    {
        return '{{%module}}';
    }

    /**
     * 验证规则
     */
    public function rules()
    {
        return [
            [['project_id', 'version_id', 'title', 'status'], 'required'],
            [['project_id', 'version_id', 'creater_id', 'status', 'sort'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 250],
        ];
    }

    /**
     * 字段字典
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => '项目',
            'version_id' => '版本',
            'creater_id' => '创建者',
            'title' => '模块名称',
            'remark' => '项目描述',
            'status' => '模块状态',
            'sort' => '排序数字',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
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
