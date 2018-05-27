<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_template".
 *
 * @property int $id
 * @property int $project_id 项目id
 * @property string $header header模板，json格式
 * @property string $request 请求模板，json格式
 * @property string $response 响应模板，json格式
 * @property int $status 模板状态
 * @property int $creater_id 创建者id
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Template extends Module
{
    /**
     * 绑定数据表
     */
    public static function tableName()
    {

        return '{{%template}}';
    }

    /**
     * 验证规则
     */
    public function rules()
    {
        return [
            [['project_id', 'header', 'request', 'response', 'status', 'creater_id'], 'required'],
            [['project_id', 'status', 'creater_id'], 'integer'],
            [['header', 'request', 'response'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => '项目id',
            'header' => 'header模板',
            'request' => '请求模板',
            'response' => '响应模板',
            'status' => '模板状态',
            'creater_id' => '创建者id',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }
}
