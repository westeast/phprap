<?php

namespace app\controllers\home;

use app\models\account\RegisterForm;
use app\models\Config;
use Yii;
use yii\helpers\Url;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\models\account\LoginForm;

class AccountController extends PublicController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'register','captcha'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout','index','create','update','repassword','login_log','setting'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
//                    'logout' => ['post'],
                ],
            ],

        ];
    }

    /**
     * 会员注册
     * @return array|string
     */
    public function actionRegister()
    {

        $request  = Yii::$app->request;

        if($request->isPost){

            $model = new RegisterForm();

            if(!$model->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($model->register()) {

                return ['status' => 'success', 'message' => '注册成功', 'callback' => Url::toRoute(['project/select'])];

            } else {

                return ['status' => 'error', 'message' => $model->getError(), 'label' => $model->getLabel()];

            }

        }

        $config = Config::findOne(['type' => 'safe']);

        return $this->display('register', ['config' => $config]);

    }

    /**
     * 会员登录
     * @return array|string|Response
     */
    public function actionLogin()
    {

        $request  = Yii::$app->request;

        // 已登录用户直接挑转到项目选择页
        if(!Yii::$app->user->isGuest){

            return $this->redirect(['home/project/select']);
        }

        if($request->isPost){

            $model = new LoginForm();

            if(!$model->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($model->login()) {

                return ['status' => 'success', 'message' => '登录成功'];

            } else {

                return ['status' => 'error', 'message' => $model->getError(), 'label' => $model->getLabel()];

            }

        }

        $config = Config::findOne(['type' => 'safe']);

        return $this->display('login', ['config' => $config]);

    }

    /**
     * 退出登录
     * @return Response
     */
    public function actionLogout()
    {

        if (Yii::$app->user->isGuest || Yii::$app->user->logout()) {
            return $this->redirect(['account/login']);
        }

    }

    public function actionSetting($tab= 'profile')
    {

        $user = Yii::$app->user->identity;

        switch ($tab) {
            case 'home':
                $view = '/home/account/home';
                break;
            case 'profile':
                $view = '/home/setting/profile';
                break;
            case 'notify':
                $view = '/home/setting/notify';
                break;
            default:
                $view = '/home/account/home';
                break;
        }

        return $this->display($view, ['user' => $user]);
    }


}
