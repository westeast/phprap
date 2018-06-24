<?php
/**
 * 保存申请模型
 */
namespace app\models\apply;

use app\models\Apply;
use Yii;

class StoreApply extends Apply
{

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['project_id', 'user_id', 'status'], 'integer'],

            ['project_id', 'validateProject'],


            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['status'], 'default', 'value'  => self::CHECK_STATUS],

            [['project_id', 'user_id'], 'required', 'on' => ['create', 'update']],

        ];

    }

    /**
     * 验证申请是否唯一
     * @param $attribute
     */
    public function validateProject($attribute)
    {
        $query = self::find();

        $query->andFilterWhere([
            'user_id' => Yii::$app->user->identity->id,
            'status' => self::CHECK_STATUS,
            'project_id'   => $this->project_id,
        ]);

        $query->andFilterWhere([
            '<>','id', $this->id,
        ]);

        if($query->exists()){
            $this->addError($attribute, '抱歉，正在审核中，请耐心等待审核结果');
        }

    }

    /**
     * 保存申请
     * @return bool
     */
    public function store()
    {

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        $this->user_id = Yii::$app->user->identity->id;

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
