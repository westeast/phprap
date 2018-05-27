<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_env".
 *
 * @property int $id
 * @property string $encode_id 加密id
 * @property string $title 环境名称
 * @property string $name 环境标识
 * @property string $base_url 环境根路径
 * @property int $sort 环境排序
 * @property int $status 环境状态
 * @property int $project_id 项目id
 * @property int $creater_id 创建者id
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Env extends Model
{

    /**
     * 默认环境
     * @var array
     */
    public $default_envs = [
        1 => [
            'name'  => 'product',
            'title' => '生产环境',
        ],
        2 => [
            'name'  => 'develop',
            'title' => '开发环境',
        ],
        3 => [
            'name'  => 'test',
            'title' => '测试环境',
        ]
    ];

    /**
     * 绑定数据表
     */
    public static function tableName()
    {
        return '{{%env}}';
    }

    /**
     * 验证规则
     */
    public function rules()
    {
        return [
            [['sort', 'status', 'project_id', 'creater_id'], 'integer'],
            [['encode_id', 'name'], 'string', 'max' => 10],
            [['title'], 'string', 'max' => 50],
            [['base_url'], 'string', 'max' => 250],
            [['encode_id'], 'unique'],

            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['status'], 'default', 'value'  => self::ACTIVE_STATUS],

            [['encode_id', 'title', 'name', 'base_url', 'project_id', 'creater_id'], 'required'],
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
            'encode_id' => '加密id',
            'name' => '环境标识',
            'title' => '环境名称',
            'base_url' => '环境根路径',
            'status' => '环境状态',
            'sort' => '环境排序',
            'project_id' => '项目id',
            'creater_id' => '创建者id',
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
     * 获取项目
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(),['id'=>'project_id']);
    }

    /**
     * 创建还不存在的下个环境
     * @return string
     */
    public function getNextEnv()
    {

        $query = self::find();

        $filter = [
            'project_id' => $this->project_id,
            'status'     => self::ACTIVE_STATUS,
        ];

        $filter['name'] = 'product';
        if(!$query->where($filter)->exists()){

            return $this->default_envs[1];
        }

        $filter['name'] = 'develop';
        if(!$query->where($filter)->exists()){
            return $this->default_envs[2];
        }

        $filter['name'] = 'test';
        if(!$query->where($filter)->exists()){
            return $this->default_envs[3];
        }

    }
}
