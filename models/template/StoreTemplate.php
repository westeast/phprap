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

        // 判断是否有更新
        $dirtyAttributes = $this->getDirtyAttributes();

        if(!$dirtyAttributes){
            return true;
        }

        if(!$this->save()){
            $transaction->rollBack();
            return false;
        }

        // 记录日志
        $log = StoreLog::findModel();

        if($this->scenario == 'create'){

            $log->method  = 'create';
            $log->content = '创建了 <code>默认模板</code>';

        }elseif($this->scenario == 'update'){

            $log->method  = 'update';
            $log->content = '更新了 <code>默认模板</code>';

        }

        $log->project_id  = $this->project_id;
        $log->object_name = 'template';
        $log->object_id   = $this->id;

        if(!$log->store()){
            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;
    }

}
