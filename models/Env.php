<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_env".
 *
 * @property int $id
 * @property string $name 环境标识
 * @property string $title 环境名
 * @property string $domain 环境域名
 * @property int $status 启用状态 10:正常 20:删除
 * @property int $project_id 项目id
 * @property int $creater_id 创建者id
 * @property string $created_at
 * @property string $updated_at
 */
class Env extends Model
{

    const ACTIVE_STATUS  = 10;
    const DELETED_STATUS = 20;

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
            [['name', 'title', 'domain', 'project_id', 'creater_id'], 'required'],
            [['status', 'project_id', 'creater_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 10],
            [['title'], 'string', 'max' => 50],
            [['domain'], 'string', 'max' => 250],
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
