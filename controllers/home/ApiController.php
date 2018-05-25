<?php
namespace app\controllers\home;

use app\models\Api;
use app\models\api\DeleteApi;
use app\models\api\StoreApi;
use app\models\Module;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Site controller
 */
class ApiController extends PublicController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'select','delete', 'show', 'debug'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],

        ];
    }

    /**
     * 接口详情
     * @param $id
     * @return string
     */
    public function actionShow($id, $tab = 'home')
    {

        $api = Api::findModel($id);

        $project = $api->module->project;

        // 获取当前版本
        $project->current_version = $api->module->version;

        switch ($tab) {
            case 'home':
                $view  = '/home/api/home';
                break;
            case 'debug':
                $view  = '/home/api/debug';
                break;
            case 'history':
                $view  = '/home/history/api';
                break;
            default:
                $view  = '/home/api/home';
                break;
        }

        return $this->display($view, ['project' => $project, 'api' => $api]);

    }

    public function actionDebug($id)
    {

        $api = Api::findModel($id);

        $project = $api->module->project;

        // 获取当前版本
        $project->current_version = $api->module->version;

        return $this->display('debug', ['project' => $project, 'api' => $api]);

    }

    /**
     * 添加接口
     * @return string
     */
    public function actionCreate($module_id)
    {

        $request  = Yii::$app->request;
        $response = Yii::$app->response;

        $api    = StoreApi::findModel();
        $module = Module::findModel($module_id);

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            $api->scenario = 'create';

            if(!$api->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            $api->version_id = $module->version_id;
            $api->module_id  = $module->id;

            if ($api->store()) {

                return ['status' => 'success', 'message' => '创建成功'];

            }

            return ['status' => 'error', 'model' => $api];

        }

        return $this->display('create', ['api' => $api, 'module' => $module]);

    }

    /**
     * 编辑接口
     * @param $id
     * @return array|string
     */
    public function actionUpdate($id)
    {

        $request  = Yii::$app->request;
        $response = Yii::$app->response;

        $api = StoreApi::findModel($id);


        return $this->display('create', ['api' => $api]);

    }

    public function actionDelete($id)
    {

        $request  = Yii::$app->request;
        $response = Yii::$app->response;

        $api  = DeleteApi::findModel($id);

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            if(!$api->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($api->delete()) {

                $callback = url('home/project/show', ['token' => $api->module->version->token]);

                return ['code' => 200, 'msg' => '删除成功', 'callback' => $callback];

            }

            return ['status' => 'error', 'model' => $api];

        }

        return $this->display('delete', ['api' => $api]);

    }


}
