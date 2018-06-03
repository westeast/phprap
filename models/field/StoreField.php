<?php

namespace app\models\field;

use app\models\Field;
use app\models\projectLog\StoreLog;
use Yii;

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

        // 判断是否有更新
        $oldAttributes   = $this->getOldAttributes();
        $dirtyAttributes = $this->getDirtyAttributes();

        if(!$dirtyAttributes){
            return true;
        }

        if(!$this->save(false)){
            $transaction->rollBack();
            return false;
        }

        // 记录日志
        $log = StoreLog::findModel();

        if($this->scenario == 'create'){

            $log->method  = 'create';
            $log->content = '创建了 字段';

        }elseif($this->scenario == 'update'){

            $log->method  = 'update';
            $log->content = '更新了 字段';

//            $log->content = $this->getUpdateContent($oldAttributes, $dirtyAttributes);

        }

        $log->project_id  = $this->api->module->project->id;
        $log->module_id   = $this->api->module->id;
        $log->api_id      = $this->api->id;
        $log->version_id  = $this->api->module->version->id;
        $log->version_name = $this->api->module->version->name;
        $log->object_name = 'field';
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
