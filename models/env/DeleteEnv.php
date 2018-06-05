<?php
/**
 * 删除环境模型
 */
namespace app\models\env;

use Yii;
use app\models\Env;

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

        // 事务提交
        $transaction->commit();

        return true;

    }

}
