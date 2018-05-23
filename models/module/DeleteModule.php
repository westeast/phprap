<?php

namespace app\models\module;

use app\models\Module;
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

    public function delete()
    {

        $this->status = self::DELETED_STATUS;

        if(!$this->validate()){
            return false;
        }

        if($this->save(false)){
            return true;
        }

        return false;

    }

}
