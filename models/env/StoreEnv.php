<?php
/**
 * 保存环境模型
 */
namespace app\models\env;

use Yii;
use app\models\Env;

class StoreEnv extends Env
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['sort'], 'filter', 'filter' => 'intval'], //此规则必须，否则就算模型里该字段没有修改，也会出现在脏属性里
            [['status', 'project_id', 'creater_id'], 'integer'],
            [['encode_id', 'name'], 'string', 'max' => 10],
            [['title'], 'string', 'max' => 50],
            [['base_url'], 'string', 'max' => 250],
            [['encode_id'], 'unique'],

            [['!created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['!creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['!encode_id'], 'default', 'value'  => $this->createEncodeId(), 'on' => 'create'],
            [['!status'], 'default', 'value'  => self::ACTIVE_STATUS, 'on' => 'create'],

            [['!encode_id', 'title', 'name', 'base_url', '!project_id', '!creater_id'], 'required', 'on' => ['create', 'update']],

        ];
    }

    /**
     * 保存环境
     * @return bool
     */
    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        $this->base_url = trim($this->base_url, '/');

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
