<?php

namespace app\models\template;

use app\models\projectLog\StoreLog;
use app\models\Template;
use Yii;

class StoreTemplate extends Template
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['project_id', 'status', 'creater_id'], 'integer'],
            [['header_json', 'request_json', 'response_json'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['encode_id'], 'string', 'max' => 10],
            [['project_id'], 'unique'],
            [['encode_id'], 'unique'],

            [['!created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['!creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['!encode_id'], 'default', 'value'  => $this->createEncodeId(), 'on' => 'create'],
            [['!status'], 'default', 'value'  => self::ACTIVE_STATUS, 'on' => 'create'],

            [['encode_id', 'project_id', 'status', 'creater_id'], 'required', 'on' => ['create', 'update']],
        ];
    }

    /**
     * 保存模板
     * @return bool
     */
    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        if(!$this->validate()){
            return false;
        }

        if(!$this->save()){
            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;
    }

}
