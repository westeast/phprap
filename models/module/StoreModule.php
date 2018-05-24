<?php

namespace app\models\module;

use app\models\history\StoreHistory;
use app\models\Module;
use Yii;
use yii\db\Exception;

class StoreModule extends Module
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['project_id', 'version_id', 'title'], 'required', 'on' => ['create', 'update']],
            [['project_id', 'version_id', 'creater_id', 'status', 'sort'], 'integer'],
            [['title', 'remark'], 'string', 'max' => 50, 'message' => '项目标题不能超过50个字符'],
            ['title', 'validateTitle'],
            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
        ];

    }

    /**
     * 验证模块名是否唯一
     * @param $attribute
     */
    public function validateTitle($attribute)
    {
        $query = self::find();

        $query->andFilterWhere([
            'project_id' => $this->project_id,
            'version_id' => $this->version_id,
            'status' => self::ACTIVE_STATUS,
            'title'  => $this->title,
        ]);

        $query->andFilterWhere([
            '<>','id', $this->id,
        ]);

        if($query->exists()){
            $this->addError($attribute, '抱歉，该模块名称已存在');
        }

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
                $log->method = '创建';

            }elseif($this->scenario == 'update'){
                $log->method = '更新';

            }

            $log->res_name = 'project';
            $log->res_id   = $this->project_id;
            $log->object   = 'module';
            $log->content  = $log->method . '了模块<code>' . $this->title . '</code>';

            if(!$log->store()){

                throw new Exception($log->getError());
            }

            // 事务提交
            $transaction->commit();

            return true;

        } catch (Exception $e) {

            $this->addError('module', $e->getMessage());

            // 事务回滚
            $transaction->rollBack();

            return false;

        }

    }

}
