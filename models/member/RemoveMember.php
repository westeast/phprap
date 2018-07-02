<?php
/**
 * 移除成员模型
 */
namespace app\models\member;

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

        // 事务提交
        $transaction->commit();

        return true;

    }

}
