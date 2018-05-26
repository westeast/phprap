<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_project_log".
 *
 * @property int $id
 * @property int $project_id 项目id
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
            [['project_id', 'user_id', 'user_name', 'user_email', 'method', 'object_name', 'object_id', 'content'], 'required'],
            [['project_id', 'user_id', 'version_id', 'object_id'], 'integer'],
            [['content'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_name', 'user_email'], 'string', 'max' => 50],
            [['version_name'], 'string', 'max' => 255],
            [['method'], 'string', 'max' => 10],
            [['object_name'], 'string', 'max' => 20],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],
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
            'user_id' => '操作人ID',
            'user_name' => '操作人昵称',
            'user_email' => '操作人邮箱',
            'version_id' => '操作版本ID',
            'version_name' => '操作版本号',
            'method' => '操作方式',
            'object_name' => '操作对象',
            'object_id' => '操作对象ID',
            'content' => '操作内容',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 获取操作方式文案
     * @return mixed
     */
    public function getMethodText()
    {

        $find = ['look','create','update', 'transfer', 'export', 'delete', 'remove'];

        $replace = ['查看','添加', '更新', '转让', '导出', '删除', '移除'];

        return str_replace($find, $replace, $this->method);
    }

    /**
     * 获取操作对象文案
     * @return mixed
     */
    public function getObjectText()
    {

        $find = ['project','version','env', 'module', 'api', 'field', 'member'];

        $replace = ['项目','版本', '环境', '模块', '接口', '字段', '成员'];

        return str_replace($find, $replace, $this->object_name);
    }
}
