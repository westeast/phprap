<?php

namespace app\models\api;

use Yii;
use app\models\Api;

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

    /**
     * 验证登录密码是否正确
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
     * 删除接口
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

        $this->status = self::DELETED_STATUS;

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
