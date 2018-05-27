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
            [['sort'], 'filter', 'filter' => 'intval'], //此规则必须，否则就算模型里该字段没有修改，也会出现在脏属性里
            [['project_id', 'version_id', 'module_id', 'status', 'sort', 'creater_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['encode_id', 'method'], 'string', 'max' => 10],
            [['title', 'uri', 'remark'], 'string', 'max' => 250],
            [['encode_id'], 'unique'],
            ['title', 'validateTitle'],

            [['!created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['!creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['!encode_id'], 'default', 'value'  => $this->createEncodeId(), 'on' => 'create'],
            [['!status'], 'default', 'value'  => self::ACTIVE_STATUS, 'on' => 'create'],

            [['encode_id', 'project_id', 'version_id', 'module_id', 'title', 'method', 'uri', 'status', 'sort', 'creater_id'], 'required', 'on' => ['create', 'update']],
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
            'creater_id' => Yii::$app->user->identity->id,
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

            $log->content = $this->getUpdateContent($oldAttributes, $dirtyAttributes, $oldAttributes['title']);

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
