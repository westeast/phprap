<?php

namespace app\models\member;

use app\models\history\StoreHistory;
use Yii;
use app\models\Member;

class RemoveMember extends Member
{

    public function remove()
    {

        // 开启事务
        $transaction  = Yii::$app->db->beginTransaction();

        if(!$this->delete()){

            return false;
        }

        // 记录日志
        $log = StoreHistory::findModel();

        $log->method    = 'remove';
        $log->res_name  = 'project';
        $log->res_id    = $this->project_id;
        $log->object    = 'member';
        $log->object_id = $this->id;
        $log->content   = '移除了成员<code>' . $this->user->fullName . '</code>';

        if(!$log->store()){
            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;

    }

}
