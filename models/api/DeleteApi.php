<?php

namespace app\models\api;

use app\models\Api;
use app\models\projectLog\SearchLog;
use app\models\projectLog\StoreLog;
use Yii;

class DeleteApi extends Api
{

    public $password;

    public function rules()
    {
        return [
            ['password', 'required', 'message' => '密码不可以为空'],
            ['password', 'validatePassword'],
        ];
    }

    public function validatePassword($attribute)
    {

        if (!$this->hasErrors()) {

            $user = Yii::$app->user->identity;

            if (!$user || !$user->validatePassword($this->password)) {

                $this->addError($attribute, '登录密码验证失败');
            }

        }
    }

    public function delete()
    {

        // 开启事务
        $transaction  = Yii::$app->db->beginTransaction();

        $this->status = self::DISABLE_STATUS;

        if(!$this->validate()){
            return false;
        }

        $this->status = self::DELETED_STATUS;

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
        $log->object_name = 'api';
        $log->object_id   = $this->id;
        $log->content     = '删除了 接口 <code>' . $this->title . '</code>';

        if(!$log->store()){
            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;

    }

}
