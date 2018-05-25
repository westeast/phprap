<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_member".
 *
 * @property int $id
 * @property int $project_id 项目id
 * @property int $user_id 用户id
 * @property string $project_rule 项目权限
 * @property string $version_rule 版本权限
 * @property string $module_rule 权限
 * @property string $api_rule 接口权限
 * @property string $member_rule 成员权限
 * @property string $map_rule 数据字典权限
 * @property int $creater_id 创建者id
 * @property string $created_at 创建时间
 * @property string $updated_at 更新时间
 */
class Member extends Model
{

    /**
     * 绑定数据表
     */
    public static function tableName()
    {
        return '{{%member}}';
    }

    /**
     * 默认验证规则
     * @return array
     */
    public function rules()
    {
        return [
            [['project_id', 'user_id', 'creater_id'], 'integer'],
            [['creater_id'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['project_rule', 'version_rule', 'module_rule', 'api_rule', 'member_rule', 'map_rule'], 'string', 'max' => 100],
        ];
    }

    /**
     * 字段映射
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => '创建者id',
            'user_id' => '用户id',
            'project_rule' => '项目权限',
            'version_rule' => '版本权限',
            'module_rule' => 'M模块取消奶奶',
            'api_rule' => '接口权限',
            'member_rule' => '成员权限',
            'map_rule' => '数据字典权限',
            'creater_id' => '创建者id',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

    /**
     * 获取关联项目
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(),['id'=>'project_id']);
    }

    /**
     * 获取审核者
     * @return \yii\db\ActiveQuery
     */
    public function getCreater()
    {
        return $this->hasOne(User::className(),['id'=>'creater_id']);
    }

    /**
     * 获取关联用户
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(),['id'=>'user_id']);
    }

    /**
     * 判断是否有权限
     * @param $type
     * @param $rule
     * @return bool|int
     */
    public function hasRule($type, $rule)
    {

        $type = $type . '_rule';

        if(in_array($rule, explode(',', $this->$type))){

            return true;
        }

        return false;

    }

    /**
     * 获取权限文案
     * @param $type
     * @return string
     */
    public function getRuleText($type)
    {

        $type = $type . '_rule';

        $find = ['look,','create,','update,', 'transfer,', 'export,', 'delete,', 'remove,'];

        $replace = ['查看、','添加、', '编辑、', '转让、', '导出、', '删除、', '移除、'];

        $title   = $this->$type ? str_replace($find, $replace, $this->$type . ',') : '';

        return trim($title, '、');
    }

}