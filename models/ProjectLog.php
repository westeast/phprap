<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%project_log}}".
 *
 * @property int $id
 * @property int $project_id 项目id
 * @property int $module_id 模块id
 * @property int $api_id 接口id
 * @property int $user_id 操作人id
 * @property string $user_name 操作人昵称
 * @property string $user_email 操作人邮箱
 * @property int $version_id 操作版本id
 * @property string $version_name 操作版本号
 * @property string $method 操作方式
 * @property string $object_name 操作对象
 * @property int $object_id 操作对象id
 * @property string $content 操作内容
 * @property string $created_at
 * @property string $updated_at
 */
class ProjectLog extends Module
{
    /**
     * 操作方式标签
     * @var array
     */
    public $methodLabels = [
        'look' => '查看',
        'create' => '添加',
        'update' => '更新',
        'transfer' => '转让',
        'export' => '导出',
        'delete' => '删除',
        'remove' => '移除',
    ];

    /**
     * 操作对象标签
     * @var array
     */
    public $objectLabels = [
        'project' => '项目',
        'version' => '版本',
        'env' => '环境',
        'module' => '模块',
        'api' => '接口',
        'field' => '字段',
        'member' => '成员',
        'template' => '模板',
    ];

    /**
     * 绑定数据表
     */
    public static function tableName()
    {

        return '{{%project_log}}';
    }

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['project_id', 'module_id', 'api_id', 'user_id', 'version_id', 'object_id'], 'integer'],
            [['content'], 'string'],
            [['user_name', 'user_email'], 'string', 'max' => 50],
            [['version_name'], 'string', 'max' => 255],
            [['method'], 'string', 'max' => 10],
            [['object_name'], 'string', 'max' => 20],

            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],

            [['project_id', 'user_id', 'user_name', 'user_email', 'method', 'object_name', 'object_id', 'content'], 'required'],

        ];

    }

    /**
     * 字段字典
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => '项目ID',
            'module_id' => '模块ID',
            'api_id' => '接口ID',
            'user_id' => '操作人ID',
            'user_name' => '操作人昵称',
            'user_email' => '操作人账号',
            'version_id' => '版本ID',
            'version_name' => '版本名',
            'method' => '操作方式',
            'object_name' => '操作对象名',
            'object_id' => '操作对象ID',
            'content' => '操作内容',
            'created_at' => '操作时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 获取当前操作方式标签
     * @return mixed
     */
    public function getMethodLabel()
    {

        return $this->methodLabels[$this->method];
    }

    /**
     * 获取当前操作对象标签
     * @return mixed
     */
    public function getObjectLabel()
    {

        return $this->objectLabels[$this->object_name];
    }

}
