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

    public function actions(){
        return [
            'captcha' => [
                'class' => 'app\actions\CaptchaAction',
                'maxLength' => 5,
                'minLength' => 5,
                'padding' => 3,
                'height' => 35,
                'width'  => 100,
                'offset' => 1
            ]
        ];
    }

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
        $response = Yii::$app->response;

        if($request->isPost){

            $model = new RegisterForm();

            $response->format = Response::FORMAT_JSON;

            if ($model->load($request->post()) && $model->register()) {

                return ['code' => 200, 'msg' => '注册成功', 'callback' => Url::toRoute(['project/select'])];

            } else {

                return ['code' => 300, 'msg' => $model->getError()];

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
        $response = Yii::$app->response;

        // 已登录用户直接挑转到项目选择页
        if(!Yii::$app->user->isGuest){

            return $this->redirect(['home/project/select']);
        }

        if($request->isPost){

            $model = new LoginForm();

            $response->format = Response::FORMAT_JSON;

            if ($model->load($request->post()) && $model->login()) {

                return ['code' => 200, 'msg' => '登录成功'];

            } else {

                return ['code' => 300, 'msg' => $model->getError()];

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
