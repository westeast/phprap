<?php

namespace app\models\member;

use Yii;
use yii\db\Exception;
use app\models\Member;
use app\models\history\StoreHistory;

class StoreMember extends Member
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['!project_id', '!creater_id', 'user_id','project_rule', 'version_rule', 'module_rule', 'api_rule', 'member_rule'], 'required', 'on' => ['create', 'update']],
            [['project_id', 'user_id', 'creater_id'], 'integer'],
        ];

    }

    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        try {

            if(!$this->save()){
                throw new Exception($this->getError());
            }

            // 记录日志
            $log = StoreHistory::findModel();

            if($this->scenario == 'create'){
                $log->method = '添加';

            }elseif($this->scenario == 'update'){
                $log->method = '更新';

            }

            $log->res_name = 'project';
            $log->res_id   = $this->project->id;
            $log->object   = 'member';
            $log->content  = $log->method . '了成员<code>' . $this->user->name . '</code>';

            if(!$log->store()){

                throw new Exception($log->getError());
            }

            // 事务提交
            $transaction->commit();

            return true;

        } catch (Exception $e) {

            $this->addError('member', $e->getMessage());

            // 事务回滚
            $transaction->rollBack();

            return false;

        }

    }

}
