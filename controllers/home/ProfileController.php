<?php

namespace app\controllers\home;

use app\models\account\ProfileForm;
use app\models\account\RegisterForm;
use app\models\Config;
use Yii;
use yii\debug\models\search\Debug;
use yii\debug\Module;
use yii\debug\Panel;
use yii\helpers\Url;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use app\models\account\LoginForm;

class ProfileController extends PublicController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['home','account','notify'],
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

    public function actionHome()
    {

        $user = Yii::$app->user->identity;

        return $this->display('/home/account/home', ['user' => $user]);
    }

    public function actionAccount()
    {
        $request  = Yii::$app->request;

        $user = Yii::$app->user->identity;

        if($request->isPost){

            $model = new ProfileForm();

            if(!$model->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($model->profile()) {

                return ['status' => 'success', 'message' => '修改成功'];

            } else {

                return ['status' => 'error', 'model' => $model];

            }

        }

        return $this->display('/home/account/profile', ['user' => $user]);

    }

}
