<?php

namespace app\models\module;

use app\models\Module;
use app\models\projectLog\StoreLog;
use Yii;

class DeleteModule extends Module
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

    /**
     * 删除模块
     * @return bool
     */
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

        $log->method = 'delete';
        $log->project_id   = $this->project_id;
        $log->version_id   = $this->version->id;
        $log->version_name = $this->version->name;
        $log->object_name  = 'module';
        $log->object_id = $this->id;
        $log->content   = '删除了 模块 <code>' . $this->title . '</code>';

        if(!$log->store()){
            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;

    }

}
