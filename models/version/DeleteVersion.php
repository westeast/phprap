<?php
/**
 * 删除版本模型
 */
namespace app\models\version;

use app\models\projectLog\StoreLog;
use app\models\Version;
use Yii;

class DeleteVersion extends Version
{

    public $password;

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
     * 删除版本
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

        $log->method     = 'delete';
        $log->project_id = $this->project_id;
        $log->version_id = $this->id;
        $log->version_name = $this->name;
        $log->object_name  = 'version';
        $log->object_id    = $this->id;
        $log->content      = '删除了 版本 <code>' . $this->name . '</code>';

        if(!$log->store()){
            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;

    }

}
