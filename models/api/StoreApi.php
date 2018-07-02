<?php

namespace app\models\api;

use Yii;
use app\models\Api;

class StoreApi extends Api
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['sort'], 'filter', 'filter' => 'intval'], //此规则必须，否则就算模型里该字段没有修改，也会出现在脏属性里
            [['module_id', 'status', 'sort', 'creater_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['encode_id', 'request_method', 'response_format'], 'string', 'max' => 20],
            [['title', 'uri', 'remark'], 'string', 'max' => 250],
            [['header_field', 'request_field', 'response_field'], 'string'],
            [['encode_id'], 'unique'],
            ['title', 'validateTitle'],

            [['!created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['!creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['!encode_id'], 'default', 'value'  => $this->createEncodeId(), 'on' => 'create'],
            [['!status'], 'default', 'value'  => self::ACTIVE_STATUS, 'on' => 'create'],

            [['encode_id', 'module_id', 'title', 'request_method', 'response_format', 'uri', 'status', 'sort', 'creater_id'], 'required', 'on' => ['create', 'update']],
        ];

    }

    /**
     * 验证接口名是否唯一
     * @param $attribute
     */
    public function validateTitle($attribute)
    {
        $query = self::find();

        $query->andFilterWhere([
            'module_id' => $this->module_id,
            'status' => self::ACTIVE_STATUS,
            'title'  => $this->title,
        ]);

        $query->andFilterWhere([
            '<>','id', $this->id,
        ]);

        if($query->exists()){
            $this->addError($attribute, '抱歉，该接口名称已存在');
        }

    }

    /**
     * 保存接口
     * @return bool
     */
    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        $this->uri = '/' . trim($this->uri, '/');

        if(!$this->validate()){
            return false;
        }

        if(!$this->save(false)){
            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;

    }

}
