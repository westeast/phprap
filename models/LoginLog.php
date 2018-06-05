<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%login_log}}".
 *
 * @property int $id
 * @property int $user_id 用户id
 * @property string $user_name 用户名称
 * @property string $user_email 用户邮箱
 * @property string $ip 登录ip
 * @property string $location IP地址
 * @property string $created_at 登录时间
 * @property string $updated_at
 */
class LoginLog extends Model
{
    /**
     * 绑定数据表
     */
    public static function tableName()
    {
        return '{{%login_log}}';
    }

    /**
     * 验证规则
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['user_name', 'user_email', 'ip'], 'string', 'max' => 50],
            [['location'], 'string', 'max' => 255],

            [['created_at', 'updated_at'], 'safe'],
            [['created_at'], 'default', 'value' => date('Y-m-d H:i:s')],

            [['user_id', 'user_name', 'user_email', 'ip'], 'required']
        ];
    }

    /**
     * 字段标签
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '会员ID',
            'user_name' => 'User Name',
            'user_email' => 'User Email',
            'ip' => 'Ip',
            'location' => 'Location',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getLocation($ip = null)
    {
        if(!$ip){

            $ip = Yii::$app->request->userIP;
        }

        $url = "http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
        //调用淘宝接口获取信息
        $json = file_get_contents($url);

        $data = json_decode($json, true);

        if ($data['code']) {

            return $data['data'];

        } else {

            $country = $data['data']['country'];
            $province = $data['data']['region'];
            $city = $data['data']['city'];
            $area = $data['data']['area'];

            return $country . ' ' . $province . ' ' . $city . ' ' . $area;

        }
    }

}