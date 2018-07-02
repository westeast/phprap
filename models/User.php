<?php
namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string $email 登录邮箱
 * @property string $name 昵称
 * @property string $password_hash 密码
 * @property string $auth_key
 * @property int $type
 * @property int $status 会员状态
 * @property string $ip 注册ip
 * @property string $location IP地址
 * @property string $created_at
 * @property string $updated_at
 */
class User extends Model implements IdentityInterface
{

    const USER_TYPE  = 10; // 普通用户类型
    const ADMIN_TYPE = 20; // 管理员类型

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'email'], 'filter', 'filter' => 'trim'],
            ['name', 'required', 'message' => '用户昵称不可以为空'],
            ['name', 'string', 'min' => 2, 'max' => 50, 'message' => '用户昵称至少包含2个字符，最多50个字符'],
            ['email', 'required', 'message' => '登录邮箱不能为空'],
            ['email', 'email','message' => '邮箱格式不合法'],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => '该登录邮箱已存在'],
            ['password_hash', 'required', 'message' => '密码不可以为空'],
            ['password_hash', 'string', 'min' => 6, 'tooShort' => '密码至少填写6位'],

            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],
            ['status', 'default', 'value' => self::ACTIVE_STATUS],
            ['type', 'default', 'value' => self::USER_TYPE],
            ['ip', 'default', 'value' => Yii::$app->request->userIP],
            ['location', 'default', 'value' => $this->getLocation()],
        ];

    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {

        if(file_exists(Yii::getAlias("@runtime") . '/install/install.lock')){

            return static::findOne(['id' => $id, 'status' => self::ACTIVE_STATUS]);

        }

        return null;

    }

    /**
     * 获取当前登录用户对象
     * @return null|IdentityInterface
     */
    public static function getIdentity()
    {

        return Yii::$app->user->identity;

    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByEmail($email)
    {

        return static::findOne(['email' => $email]);

    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::ACTIVE_STATUS,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * 获取用户全名(昵称+邮箱)
     * @return string
     */
    public function getFullName()
    {
        return $this->name . '(' . $this->email . ')';
    }

    /**
     * 关联member表
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {

        return $this->hasOne(Member::className(),['user_id'=>'id']);

    }

    /**
     * 获取最近登录
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getLastLogin()
    {
        $filter = [
            'user_id' => $this->id,
        ];

        $sort = [
            'id' => SORT_DESC
        ];

        return LoginLog::find()->where($filter)->orderBy($sort)->one();
    }

    /**
     * 获取创建的项目
     * @return \yii\db\ActiveQuery
     */
    public function getMyCreatedProjects()
    {
        return $this->hasMany(Project::className(),['creater_id'=>'id']);
    }

    /**
     * 获取参与的项目
     * @return \yii\db\ActiveQuery
     */
    public function getMyJoinedProjects()
    {

        return $this->hasMany(Project::className(), ['id' => 'project_id'])
            ->viaTable('{{%member}}', ['user_id' => 'id']);

    }

    /**
     * 判断是否是系统管理员
     * @return bool
     */
    public function getIsAdmin()
    {
        return $this->type == self::ADMIN_TYPE ? true : false;
    }

}
