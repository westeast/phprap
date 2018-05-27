<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_module".
 *
 * @property int $id
 * @property string $encode_id 加密id
 * @property int $project_id 项目id
 * @property int $version_id 版本id
 * @property string $title 模块名称
 * @property string $remark 项目描述
 * @property int $status 模块状态
 * @property int $sort 模块排序
 * @property int $creater_id 创建者id
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Module extends Model
{

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
            [['project_id', 'version_id', 'status', 'sort', 'creater_id'], 'integer'],
            [['encode_id'], 'string', 'max' => 10],
            [['title'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 250],
            [['encode_id'], 'unique'],

            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['status'], 'default', 'value'  => self::ACTIVE_STATUS],

            [['encode_id', 'project_id', 'version_id', 'title', 'status', 'creater_id'], 'required'],
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
            'encode_id'  => '加密id',
            'project_id' => '项目id',
            'version_id' => '版本id',
            'creater_id' => '创建者id',
            'title' => '模块名称',
            'remark' => '项目描述',
            'status' => '模块状态',
            'sort' => '模块排序',
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
