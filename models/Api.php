<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_api".
 *
 * @property int $id
 * @property string $encode_id 加密id
 * @property int $module_id 模块id
 * @property string $title 接口名
 * @property string $request_method 请求方式
 * @property string $response_format 响应格式
 * @property string $uri 接口地址
 * @property string $header_field header字段，json格式
 * @property string $request_field 请求字段，json格式
 * @property string $response_field 响应字段，json格式
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
     * 请求方式标签
     * @var array
     */
    public $requestMethodLabels = [
        'get' => 'GET',
        'post' => 'POST',
        'put' => 'PUT',
        'delete' => 'DELETE',
    ];

    /**
     * 响应格式标签
     * @var array
     */
    public $responseFormatLabels = [
        'json_object'=> 'JSON对象',
        'json_array' => 'JSON数组',
        'xml' => 'XML',
    ];


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
            [['module_id', 'status', 'sort', 'creater_id'], 'integer'],
            [['encode_id', 'request_method', 'response_format'], 'string', 'max' => 20],
            [['title', 'uri', 'remark'], 'string', 'max' => 250],
            [['header_field', 'request_field', 'response_field'], 'string'],
            [['encode_id'], 'unique'],

            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['status'], 'default', 'value'  => self::ACTIVE_STATUS],

            [['encode_id', 'module_id', 'title', 'request_method', 'response_format',  'uri', 'status', 'sort', 'creater_id'], 'required'],
        ];
    }

    /**
     * 字段字典
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => '接口id',
            'encode_id' => '加密id',
            'module_id' => '模块id',
            'title' => '接口名',
            'request_method' => '请求方式',
            'response_format' => '响应格式',
            'uri' => '接口地址',
            'header_filed' => 'Header字段',
            'request_field' => '请求字段',
            'response_field' => '响应字段',
            'remark' => '接口简介',
            'status' => '接口状态',
            'sort' => '接口排序',
            'creater_id' => '创建者id',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 获取当前请求方式标签
     * @return mixed
     */
    public function getRequestMethodLabel()
    {
        return $this->requestMethodLabels[$this->request_method];
    }

    /**
     * 获取当前响应格式标签
     * @return mixed
     */
    public function getResponseFormatLabel()
    {
        return $this->responseFormatLabels[$this->response_format];
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
     * 获取header数组
     * @return array
     */
    public function getHeaderAttributes()
    {
        return json_decode($this->header_field, true);

    }

    /**
     * 获取请求参数数组
     * @return array
     */
    public function getRequestAttributes()
    {
        return json_decode($this->request_field, true);
    }

    /**
     * 获取响应参数数组
     * @return array
     */
    public function getResponseAttributes()
    {
        return json_decode($this->response_field, true);
    }

    /**
     * 判断字段是否是符合类型
     * @param $field
     * @return bool
     */
    public function isComplexType($type)
    {
        return in_array($type, ['array', 'object']) ? true : false;

    }

    public function getField()
    {
        return Field::findModel();
    }

}
