<?php

namespace app\models\api;

use Yii;
use yii\db\Exception;
use app\models\Api;
use app\models\history\StoreHistory;

class StoreApi extends Api
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['module_id', 'title', 'uri', 'method', 'sort'], 'required', 'on' => ['create', 'update']],
            [['module_id', 'creater_id', 'status', 'sort'], 'integer'],
            [['title', 'remark'], 'string', 'max' => 255],
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
            'module_id' => $this->module_id,
            'status' => self::ACTIVE_STATUS,
            'title'  => $this->title,
        ]);

        $query->andFilterWhere([
            '<>','id', $this->id,
        ]);

        if($query->exists()){
            $this->addError($attribute, '抱歉，该接口名称已存在');
        }

    }

    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        try {

            $this->uri = '/' . trim($this->uri, '/');

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
            $log->res_id   = $this->module->project->id;
            $log->object   = 'api';
            $log->content  = $log->method . '了接口<code>' . $this->title . '</code>';

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
