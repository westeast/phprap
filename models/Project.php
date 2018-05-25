<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "doc_project".
 *
 * @property int $id
 * @property int $creater_id 创建者id
 * @property string $title 项目标题
 * @property string $remark 项目备注
 * @property int $allow_search 是否允许搜索
 * @property int $sort 排序数字
 * @property int $status 启用状态
 * @property string $created_at
 * @property string $updated_at
 */
class Project extends Model
{

    const ACTIVE_STATUS  = 10; //启用状态
    const DELETED_STATUS = 20; //禁用状态

    const ALLOW_SEARCH  = 10; //允许搜索
    const FORBID_SEARCH = 20; //禁止搜索

    /**
     * 当前版本号
     * @var
     */
    public $current_version;

    /**
     * 绑定数据表
     */
    public static function tableName()
    {
        return '{{%project}}';
    }

    /**
     * 获取项目创建者
     * @return \yii\db\ActiveQuery
     */
    public function getCreater()
    {
        return $this->hasOne(User::className(),['id'=>'creater_id']);
    }

    /**
     * 获取项目环境
     * @return \yii\db\ActiveQuery
     */
    public function getEnvs()
    {
        $filter = ['status' => Env::ACTIVE_STATUS];
        return $this->hasMany(Env::className(), ['project_id' => 'id'])->where($filter)->orderBy(['id' => SORT_ASC]);
    }

    /**
     * 获取项目成员
     * @return \yii\db\ActiveQuery
     */
    public function getMembers()
    {
        $sort = [
            'id' => SORT_DESC
        ];

        return $this->hasMany(Member::className(), ['project_id' => 'id'])->orderBy($sort);
    }

    /**
     * 获取非项目成员
     * @return \yii\db\ActiveQuery
     */
    public function getNotMembers($param = [])
    {

        $query = User::find();

        $query->andFilterWhere(['status' => User::ACTIVE_STATUS]);

        $query->andFilterWhere(['not in', 'id', $this->getMembers()->select('user_id')->column()])
              ->andFilterWhere(['<>', 'id', $this->creater_id]);

        $query->andFilterWhere([
            'or',
            ['like','name', $param['name']],
            ['like','email', $param['name']],
        ]);

        return $query->limit(10)->all();

    }

    /**
     * 获取项目版本
     * @return \yii\db\ActiveQuery
     */
    public function getVersions()
    {

        $filter = [
            'status' => Version::ACTIVE_STATUS
        ];

        $sort = [
            'id' => SORT_DESC
        ];

        return $this->hasMany(Version::className(), ['project_id' => 'id'])->where($filter)->orderBy($sort);
    }

    /**
     * 获取我创建的项目
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getMyCreated()
    {

        $filter = [
            'status' => self::ACTIVE_STATUS,
            'creater_id' => Yii::$app->user->identity->id,
        ];

        $sort = [
            'sort' => SORT_DESC,
            'id'   => SORT_DESC
        ];

        return self::find()->where($filter)->orderBy($sort)->all();
    }

    /**
     * 获取我参与的项目
     */
    public function getMyJoined()
    {

        $filter = [
            'status' => self::ACTIVE_STATUS,
            'creater_id' => Yii::$app->user->identity->id,
        ];

        $sort = [
            'sort' => SORT_DESC,
            'id'   => SORT_DESC
        ];

        return self::find()->where($filter)->orderBy($sort)->all();
    }

    /**
     * 获取最新版本号
     */
    public function getLastVersion()
    {

        $filter = [
            'status' => Version::ACTIVE_STATUS,
            'project_id' => $this->id,
        ];

        $sort = [
            'id' => SORT_DESC
        ];

        return Version::find()->where($filter)->orderBy($sort)->one();
    }

    /**
     * 获取当前版本号
     */
    public function getCurrentVersion()
    {

        return $this->current_version;
    }

    /**
     * 获取友好的项目更新时间，如5分钟前
     * @return string
     */
    public function getFriendUpdateAt()
    {
        $time = strtotime($this->updated_at);
        return Yii::$app->formatter->asRelativeTime($time);
    }

    /**
     * 获取是否允许搜索文案
     * @return string
     */
    public function getAllowSearchText()
    {
        if($this->allow_search == self::ALLOW_SEARCH){
            return '允许';
        }
        if($this->allow_search == self::FORBID_SEARCH){
            return '禁止';
        }
    }

    /**
     * 判断是否是项目创建者
     * @return bool
     */
    public function isCreater($user_id = 0)
    {

        $user_id = $user_id ? $user_id : Yii::$app->user->identity->id;

        return $this->creater_id == $user_id ? true : false;
    }

    /**
     * 判断是否是项目参与者
     * @return bool
     */
    public function isJoiner($user_id = 0)
    {

        $user_id = $user_id ? $user_id : Yii::$app->user->identity->id;

        $query = Member::find()->where(['project_id' => $this->id, 'user_id' => $user_id]);

        return $query->exists() ? true : false;
    }

    /**
     * 检测是否有项目某项权限，如编辑、删除
     * @param $rule
     * @return bool
     */
    public function hasRule($rule, $user_id = 0)
    {

        $user_id = $user_id ? $user_id : Yii::$app->user->identity->id;

        // 项目创建者拥有该项目所有权限
        if($this->isCreater($user_id)){
            return true;
        }

        $member = Member::findOne(['project_id' => $this->id, 'user_id' => $user_id]);

        if($this->isJoiner($user_id) && $member->hasRule('project', $rule)){
            return true;
        }

        return false;
    }

}