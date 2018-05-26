<?php

namespace app\models\api;

use app\models\projectLog\StoreLog;
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

        $this->uri = '/' . trim($this->uri, '/');

        if(!$this->validate()){
            return false;
        }

        // 判断是否有更新
        $oldAttributes   = $this->getOldAttributes();
        $dirtyAttributes = $this->getDirtyAttributes();

        if(!$dirtyAttributes){
            return true;
        }

        if(!$this->save(false)){
            $transaction->rollBack();
            return false;
        }

        // 记录日志
        $log = StoreLog::findModel();

        if($this->scenario == 'create'){
            $log->method  = 'create';
            $log->content = '创建了 接口 <code>' . $this->title . '</code>';

        }elseif($this->scenario == 'update'){

            $log->method  = 'update';

            $log->content = $this->getUpdateContent($oldAttributes, $dirtyAttributes);

        }

        $log->project_id  = $this->module->project_id;
        $log->version_id  = $this->module->version->id;
        $log->version_name  = $this->module->version->name;
        $log->object_name = 'api';
        $log->object_id   = $this->id;

        if(!$log->store()){

            $transaction->rollBack();
            return false;
        }

        // 事务提交
        $transaction->commit();

        return true;

    }

}
