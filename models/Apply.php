<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%apply}}".
 *
 * @property int $id
 * @property int $project_id 项目id
 * @property int $user_id 申请用户id
 * @property int $status 审核状态
 * @property string $created_at 添加时间
 * @property string $updated_at
 * @property string $checked_at 处理时间
 */
class Apply extends Model
{

    const CHECK_STATUS = 10; //待审核状态
    const PASS_STATUS  = 20; //审核通过状态
    const REFUSE_STATUS = 30; //审核拒绝状态

    /**
     * 绑定数据表
     */
    public static function tableName()
    {
        return '{{%apply}}';
    }

    /**
     * 验证规则
     */
    public function rules()
    {

        return [
            [['project_id', 'user_id', 'status'], 'integer'],

            [['created_at', 'updated_at', 'checked_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            [['status'], 'default', 'value'  => self::CHECK_STATUS],

            [['project_id', 'user_id'], 'required'],

        ];

    }

    /**
     * 字段标签
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'user_id' => 'User ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'checked_at' => 'Checked At',
        ];
    }

    /**
     * 获取项目
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(),['id'=>'project_id']);
    }

    /**
     * 获取申请者
     * @return \yii\db\ActiveQuery
     */
    public function getApplier()
    {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }
}
