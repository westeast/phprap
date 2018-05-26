<?php

namespace app\models\project;

use app\models\Project;
use app\models\projectLog\StoreLog;
use Yii;

class DeleteProject extends Project
{

    public $password;

    /**
     * 验证规则
     * @return array
     */
    public function rules()
    {
        return [
            ['password', 'required', 'message' => '登录密码不可以为空'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * 验证密码是否正确
     * @param $attribute
     */
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
     * 删除项目
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

        $log->method      = 'delete';
        $log->project_id  = $this->id;
        $log->object_name = 'project';
        $log->object_id   = $this->id;
        $log->content     = '删除了 项目 <code>' . $this->title . '</code>';

        if(!$log->store()){
            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;

    }

}
