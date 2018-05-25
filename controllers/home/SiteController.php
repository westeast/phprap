<?php
namespace app\controllers\home;

use app\models\Config;
use app\models\Env;
use app\models\LoginLog;
use app\models\Project;
use app\models\project\StoreProject;
use app\models\version\StoreVersion;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;


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

        $project = StoreProject::findModel();

        Yii::$app->response->format = Response::FORMAT_JSON;

        $a = Yii::$app->db->transaction(function() use($project) {

//            Yii::$app->response->format = Response::FORMAT_JSON;


            $project->scenario = 'create';

            $project->title = '8888888';
            $project->remark = '5555';
            $project->allow_search = 1;


            if(!$project->store()){
                return ['status' => 'error', 'model' => $project->getError()];
            }

            // 添加默认版本
            $version = StoreVersion::findModel();

            $version->scenario   = 'create';
            $version->project_id = $project->id;
            $version->parent_id  = 0;
            $version->remark     = '初始版本';
            $version->name = '3.33333';

            if(!$version->store()){
                return ['status' => 'error', 'model' => $version];
            }


            return ['status' => 'success', 'message' => '创建成功'];

        });

        return $a;
    }

}
