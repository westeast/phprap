<?php
namespace app\controllers\home;

use app\models\Api;
use app\models\api\StoreApi;
use app\models\Module;
use app\models\module\StoreModule;
use app\models\Version;
use app\models\version\DeleteVersion;
use app\models\version\SearchVersion;
use app\models\version\StoreVersion;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Site controller
 */
class DebugController extends PublicController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'select','delete', 'home'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],

        ];
    }

    /**
     * 接口主页
     * @param $id
     * @return string
     */
    public function actionHome($id)
    {

        $api = Api::findModel($id);

        $project = $api->module->project;

        // 获取当前版本
        $project->current_version = $api->module->version;

        return $this->display('home', ['project' => $project, 'api' => $api]);

    }

}
