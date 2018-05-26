<?php

namespace app\models\env;

use app\models\Env;
use app\models\projectLog\StoreLog;
use Yii;

class DeleteEnv extends Env
{

    public function delete()
    {

        // 开启事务
        $transaction  = Yii::$app->db->beginTransaction();

        $this->status = self::DISABLE_STATUS;

        if(!$this->validate()){
            return false;
        }

        if(!$this->save(false)){
            $transaction->rollBack();
            return false;
        }

        // 记录日志
        $log = StoreLog::findModel();

        $log->method      = 'delete';
        $log->project_id  = $this->project_id;
        $log->object_name = 'env';
        $log->object_id   = $this->id;
        $log->content     = '删除了环境<code>' . $this->title . '</code>';

        if(!$log->store()){
            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;

    }

}
