<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_field".
 *
 * @property int $id
 * @property string $encode_id
 * @property int $api_id 接口id
 * @property string $header_json header字段，json格式
 * @property string $request_json 请求字段，json格式
 * @property string $response_json 响应字段，json格式
 * @property int $creater_id 创建者id
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Field extends Model
{

    /**
     * 字段类型标签
     * @var array
     */
    public $typeLabels = [
        'string' => '字符串(string)',
        'integer' => '整数(integer)',
        'float'   => '小数(float)',
        'boolean' => '布尔(boolean)',
        'object'  => '对象(object)',
        'array'   => '数组(array)',
    ];

    /**
     * 是否必须标签
     * @var array
     */
    public $requiredLabels = [
        '10' => '是',
        '20' => '否',
    ];

    /**
     * 绑定数据表
     */
    public static function tableName()
    {
        return '{{%field}}';
    }

    /**
     * 验证规则
     */
    public function rules()
    {
        return [
            [['api_id', 'creater_id', 'status'], 'integer'],
            [['header_json', 'request_json', 'response_json'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['encode_id'], 'string', 'max' => 10],
            [['encode_id'], 'unique'],

            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['status'], 'default', 'value'  => self::ACTIVE_STATUS],

            [['encode_id', 'header_json', 'request_json', 'response_json', 'creater_id'], 'required'],

        ];
    }

    /**
     * 字段标签
     */
    public function attributeLabels()
    {
        return [
            'id' => '字段id',
            'encode_id' => '加密id',
            'api_id' => '接口id',
            'header_json' => 'Header字段',
            'request_json' => '请求字段',
            'response_json' => '响应字段',
            'status' => '字段状态',
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
     * 获取关联接口
     * @return \yii\db\ActiveQuery
     */
    public function getApi()
    {
        return $this->hasOne(Api::className(),['id'=>'api_id']);
    }

    /**
     * 获取header数组
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

    /**
     * 检测参数是否允许删除
     * @param $attribute
     * @return bool
     */
    public function isAllowDetele($attribute)
    {
        return in_array($attribute, ['array', 'object']) ? true : false;
    }


}
