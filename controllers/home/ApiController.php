<?php
namespace app\controllers\home;

use app\models\Api;
use app\models\api\DeleteApi;
use app\models\api\StoreApi;
use app\models\Module;
use app\models\projectLog\SearchLog;
use Yii;
use yii\filters\AccessControl;

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

        $request = Yii::$app->request;

        $api    = StoreApi::findModel();
        $module = Module::findModel(['encode_id' => $module_id]);

        if($request->isPost){

            $api->scenario = 'create';

            if(!$api->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            $api->project_id = $module->project_id;
            $api->version_id = $module->version_id;
            $api->module_id  = $module->id;

            if ($api->store()) {

                $callback = url('home/api/show', ['id' => $api->encode_id]);

                return ['status' => 'success', 'message' => '创建成功', 'callback' => $callback];

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

        $api = StoreApi::findModel($id);

        return $this->display('create', ['api' => $api]);

    }

    public function actionDelete($id)
    {

        $request = Yii::$app->request;

        $api = DeleteApi::findModel(['encode_id' => $id]);

        if($request->isPost){

            if(!$api->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($api->delete()) {

                $callback = url('home/project/show', ['version_id' => $api->module->version->encode_id]);

                return ['status' => 'success', 'message' => '删除成功', 'callback' => $callback];

            }

            return ['status' => 'error', 'model' => $api];

        }

        return $this->display('delete', ['api' => $api]);

    }

    /**
     * 接口详情
     * @param $id
     * @return string
     */
    public function actionShow($id, $tab = 'home')
    {

        $api = Api::findModel(['encode_id' => $id]);

        if(!$api->id || !$api->hasRule('look')){
            return $this->error('抱歉，您无权查看');
        }

        $project = $api->module->project;

        // 获取当前版本
        $project->current_version = $api->module->version;

        $params = [
            'object_name' => 'api',
            'object_id'  => $id
        ];

        switch ($tab) {
            case 'home':
                $view  = '/home/api/home';
                break;
            case 'debug':
                $view  = '/home/api/debug';
                break;
            case 'log':
                $model = SearchLog::findModel()->search($params);
                $view  = '/home/log/api';
                break;
            default:
                $view  = '/home/api/home';
                break;
        }

        return $this->display($view, ['project' => $project, 'api' => $api, 'model' => $model]);

    }
}
