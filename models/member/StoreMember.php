<?php

namespace app\models\member;

use Yii;
use app\models\Member;

class StoreMember extends Member
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['project_id', 'user_id', 'creater_id'], 'integer'],
            [['encode_id'], 'string', 'max' => 10],
            [['project_rule', 'version_rule', 'module_rule', 'api_rule', 'member_rule', 'map_rule'], 'string', 'max' => 100],
            [['encode_id'], 'unique'],

            [['!created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['!creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['!encode_id'], 'default', 'value'  => $this->createEncodeId(), 'on' => 'create'],

            [['encode_id', 'project_id', 'user_id', 'creater_id'], 'required', 'on' => ['create', 'update']],

        ];

    }

    /**
     * 保存成员
     * @return bool
     */
    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

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
