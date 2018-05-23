<?php
namespace app\controllers\home;

use app\models\Config;
use app\models\Env;
use app\models\LoginLog;
use app\models\Project;
use Yii;
use yii\filters\AccessControl;


/**
 * Site controller
 */
class SiteController extends PublicController
{

    public function actions()
    {
        return [
            // 用类来申明"error" 动作
            'error' => 'yii\web\ErrorAction',

            // 用配置数组申明 "view" 动作
            'view' => [
                'class' => 'yii\web\ViewAction',
                'viewPrefix' => 'json',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index','demo'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {

        return $this->redirect(['project/select']);

    }

    public function actionDemo()
    {
        $project = uuid();

        dump($project);
    }

}
