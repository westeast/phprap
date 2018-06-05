<?php

namespace app\controllers\home;

use app\models\account\ProfileForm;
use app\models\loginLog\SearchLog;
use Yii;

use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class ProfileController extends PublicController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['home','account','log'],
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

            if ($model->store()) {

                return ['status' => 'success', 'message' => '修改成功'];

            } else {

                return ['status' => 'error', 'model' => $model];

            }

        }

        return $this->display('/home/account/profile', ['user' => $user]);

    }

    public function actionLog()
    {

        $params = Yii::$app->request->queryParams;

        $params['user_id'] = Yii::$app->user->identity->id;

        $model = SearchLog::findModel()->search($params);

        return $this->display('/home/log/login', ['model' => $model]);

    }

}
