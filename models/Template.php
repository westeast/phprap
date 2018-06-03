<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_template".
 *
 * @property int $id
 * @property string $encode_id 加密id
 * @property int $project_id 项目id
 * @property string $header_json header模板，json格式
 * @property string $request_json 请求模板，json格式
 * @property string $response_json 响应模板，json格式
 * @property int $status 模板状态
 * @property int $creater_id 创建者id
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Template extends Module
{

    /**
     * 默认模板参数
     * @var array
     */
    public $defaultAttributes = [
        'header'  => [
            ['name' => 'Content-Type', 'title' => '', 'value' => 'application/json;charset=utf-8', 'remark' => ''],
            ['name' => 'Accept', 'title' => '', 'value' => 'application/json', 'remark' => ''],
        ],
        'request' => [
            ['name' => 'token', 'title' => '令牌', 'type' => 'string', 'required' => 10, 'default' => '' ,'remark' => ''],
        ],
        'response'=> [
            ['name' => 'code', 'title' => '返回状态码', 'type' => 'number', 'mock' => ''],
            ['name' => 'message', 'title' => '返回信息', 'type' => 'string', 'mock' => ''],
            ['name' => 'data', 'title' => '数据实体', 'type' => 'array', 'mock' => '']
        ],
    ];

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
            [['project_id', 'status', 'creater_id'], 'integer'],
            [['heade_json', 'request_json', 'response_json'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['encode_id'], 'string', 'max' => 10],
            [['project_id'], 'unique'],
            [['encode_id'], 'unique'],

            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['status'], 'default', 'value'  => self::ACTIVE_STATUS],

            [['encode_id', 'project_id', 'header_json', 'request_json', 'response_json', 'status', 'creater_id'], 'required'],
        ];

    }

    /**
     * 字段字典
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'encode_id' => '加密id',
            'project_id' => '项目id',
            'header_json' => 'header模板',
            'request_json' => '请求模板',
            'response_json' => '响应模板',
            'status' => '模板状态',
            'creater_id' => '创建者id',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 获取字段模型
     * @return array
     */
    public function getField()
    {
        return Field::findModel();
    }

    /**
     * 获取header参数数组
     * @return array
     */
    public function getHeaderAttributes()
    {
        return json_decode($this->header_json, true);

    }

    /**
     * 获取请求参数数组
     * @return array
     */
    public function getRequestAttributes()
    {
        return json_decode($this->request_json, true);
    }

    /**
     * 获取响应参数数组
     * @return array
     */
    public function getResponseAttributes()
    {
        return json_decode($this->response_json, true);
    }

}
