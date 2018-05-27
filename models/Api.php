<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_api".
 *
 * @property int $id
 * @property string $encode_id 加密id
 * @property int $project_id 项目id
 * @property int $version_id 版本id
 * @property int $module_id 模块id
 * @property string $title 接口名
 * @property string $method 请求方式
 * @property string $uri 接口地址
 * @property string $remark 接口简介
 * @property int $status 接口状态
 * @property int $sort 接口排序
 * @property int $creater_id 创建者id
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Api extends Model
{

    /**
     * 绑定数据表
     */
    public static function tableName()
    {
        return '{{%api}}';
    }

    /**
     * 验证规则
     * @return array
     */
    public function rules()
    {
        return [
            [['project_id', 'version_id', 'module_id', 'status', 'sort', 'creater_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['encode_id', 'method'], 'string', 'max' => 10],
            [['title', 'uri', 'remark'], 'string', 'max' => 250],
            [['encode_id'], 'unique'],

            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['status'], 'default', 'value'  => self::ACTIVE_STATUS],

            [['encode_id', 'project_id', 'version_id', 'module_id', 'title', 'method', 'uri', 'status', 'sort', 'creater_id'], 'required'],
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
            'project_id' => 'Project ID',
            'version_id' => 'Version ID',
            'module_id' => 'Module ID',
            'title' => 'Title',
            'method' => 'Method',
            'uri' => 'Uri',
            'remark' => 'Remark',
            'status' => 'Status',
            'sort' => 'Sort',
            'creater_id' => 'Creater ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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

    /**
     * 判断是否有权限
     * @param $rule
     * @param int $user_id
     * @return bool
     */
    public function hasRule($rule, $user_id = 0)
    {

        $user_id = $user_id ? $user_id : Yii::$app->user->identity->id;

        // 非启用状态没有权限
        if($this->status != self::ACTIVE_STATUS){
            return false;
        }

        // 项目创建者拥有该项目所有权限
        if($this->project->isCreater($user_id)){
            return true;
        }

        $member = Member::findOne(['project_id' => $this->id, 'user_id' => $user_id]);

        if($this->project->isJoiner($user_id) && $member->hasRule('api', $rule)){
            return true;
        }

        return false;
    }
}
