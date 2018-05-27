<?php

namespace app\models\member;

use app\models\projectLog\StoreLog;
use Yii;
use app\models\Member;

class StoreMember extends Member
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['project_id', 'user_id', 'creater_id'], 'integer'],
            [['encode_id'], 'string', 'max' => 10],
            [['project_rule', 'version_rule', 'module_rule', 'api_rule', 'member_rule', 'map_rule'], 'string', 'max' => 100],
            [['encode_id'], 'unique'],

            [['!created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['!creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['!encode_id'], 'default', 'value'  => $this->createEncodeId(), 'on' => 'create'],

            [['encode_id', 'project_id', 'user_id', 'creater_id'], 'required', 'on' => ['create', 'update']],

        ];

    }

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

            $log->method   = 'create';
            $log->content  = '添加了 成员 <code>' . $this->user->fullName . '</code>';

        }elseif($this->scenario == 'update'){

            $log->method  = 'update';

            $log->content = $this->getUpdateContent($oldAttributes, $dirtyAttributes);

        }

        $log->project_id  = $this->project->id;
        $log->object_name = 'member';
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
