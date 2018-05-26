<?php

namespace app\models\member;

use app\models\projectLog\StoreLog;
use Yii;
use app\models\Member;
use app\models\history\StoreHistory;

class StoreMember extends Member
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['!project_id', '!creater_id', 'user_id','project_rule', 'version_rule', 'module_rule', 'api_rule', 'member_rule'], 'required', 'on' => ['create', 'update']],
            [['project_id', 'user_id', 'creater_id'], 'integer'],
        ];

    }

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

        // 记录日志
        $log = StoreLog::findModel();

        if($this->scenario == 'create'){

            $log->method   = 'create';
            $log->content  = '添加了 成员 <code>' . $this->user->fullName . '</code>';

        }elseif($this->scenario == 'update'){

            $log->method  = 'update';
            $log->content = '更新了 成员 <code>' . $this->user->fullName . '</code>';

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
