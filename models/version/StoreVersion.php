<?php

namespace app\models\version;

use app\models\api\StoreApi;
use app\models\Module;
use app\models\module\StoreModule;
use Yii;
use app\models\Version;

class StoreVersion extends Version
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['remark'], 'string', 'max' => 250],
            ['name', 'validateName'],
            [['project_id', 'creater_id', 'parent_id'], 'integer', 'on' => ['create', 'update']],

            [['!created_at'], 'default', 'value' => date('Y-m-d H:i:s'), 'on' => 'create'],
            [['!creater_id'], 'default', 'value' => Yii::$app->user->identity->id, 'on' => 'create'],
            [['!encode_id'], 'default', 'value'  => $this->createEncodeId(), 'on' => 'create'],
            [['!status'], 'default', 'value'  => self::ACTIVE_STATUS, 'on' => 'create'],

            [['!encode_id', '!project_id', '!creater_id', 'name', '!status'], 'required', 'on' => ['create', 'update']],
        ];
    }

    /**
     * 验证版本号是否唯一
     * @param $attribute
     */
    public function validateName($attribute)
    {
        $query = self::find();

        $query->andFilterWhere([
            'project_id' => $this->project_id,
            'status' => self::ACTIVE_STATUS,
            'name'   => $this->name,
        ]);

        $query->andFilterWhere([
            '<>','id', $this->id,
        ]);

        if($query->exists()){
            $this->addError($attribute, '抱歉，该版本号已存在');
        }

    }

    /**
     * 保存版本
     * @return bool
     */
    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        if($this->scenario == 'create' && $this->parent_id){

            $parent_version = self::findModel(['encode_id' => $this->parent_id]);

            $this->parent_id = $parent_version->id;
        }

        if(!$this->validate()){
            return false;
        }

        if(!$this->save()){
            $transaction->rollBack();
            return false;
        }

        if($this->scenario == 'create' && $parent_version->id){
            // 循环插入模块
            foreach ($parent_version->modules as $module) {

                $_module = StoreModule::findModel();

                $_module->setAttributes($module->attributes);

                $_module->encode_id  = $this->createEncodeId();
                $_module->version_id = $this->id;

                if(!$_module->store()){
                    $transaction->rollBack();
                    return false;
                }

                // 循环插入接口
                foreach ($module->apis as $api) {
                    $_api = StoreApi::findModel();

                    $_api->setAttributes($api->attributes);

                    $_api->encode_id = $this->createEncodeId();
                    $_api->module_id = $_module->id;

                    if(!$_api->store()){

                        $transaction->rollBack();
                        return false;
                    }

                }

            }

        }

        // 事务提交
        $transaction->commit();

        return true;
    }

}
