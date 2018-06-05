<?php
/**
 * 保存字段模型
 */
namespace app\models\field;

use Yii;
use app\models\Field;

class StoreField extends Field
{
    /**
     * 验证规则
     */
    public function rules()
    {
        return [
            [['api_id', 'creater_id'], 'integer'],
            [['header_json', 'request_json', 'response_json'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['encode_id'], 'string', 'max' => 10],
            [['encode_id'], 'unique'],

            [['!created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['!creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['!encode_id'], 'default', 'value'  => $this->createEncodeId(), 'on' => 'create'],
            [['!status'], 'default', 'value'  => self::ACTIVE_STATUS, 'on' => 'create'],

            [['encode_id', 'header_json', 'request_json', 'response_json', 'creater_id'], 'required', 'on' => ['create', 'update']],

        ];
    }

    /**
     * 保存字段
     * @return bool
     */
    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

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
