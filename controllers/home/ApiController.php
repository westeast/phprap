<?php
namespace app\controllers\home;

use app\models\Api;
use app\models\api\DeleteApi;
use app\models\api\StoreApi;
use app\models\field\StoreField;
use app\models\Module;
use app\models\Template;
use Yii;
use yii\web\Response;

/**
 * Site controller
 */
class ApiController extends PublicController
{

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

        $module = Module::findModel(['encode_id' => $module_id]);

        $api    = StoreApi::findModel();

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();

            $api->scenario = 'create';

            // 根据模板添加默认字段
            $template = Template::findModel(['project_id' => $module->project_id]);

            $api->header_field   = $template->header_field;
            $api->request_field  = $template->request_field;
            $api->response_field = $template->response_field;

            if(!$api->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            $api->module_id = $module->id;

            if(!$api->store()){

                $transaction->rollBack();
                return ['status' => 'error', 'message' => $api->getErrorMessage(), 'label' => $api->getErrorLabel()];

            }

            // 事务提交
            $transaction->commit();

            $callback = url('home/api/show', ['id' => $api->encode_id]);

            return ['status' => 'success', 'message' => '创建成功', 'callback' => $callback];

        }

        return $this->display('create', ['api' => $api, 'module' => $module]);

    }

    /**
     * 更新接口
     * @param $id
     * @return array|string
     */
    public function actionUpdate($id)
    {

        $request = Yii::$app->request;

        $api = StoreApi::findModel(['encode_id' => $id]);

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();

            $api->scenario = 'update';

            if(!$api->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if(!$api->store()){

                $transaction->rollBack();
                return ['status' => 'error', 'message' => $api->getErrorMessage(), 'label' => $api->getErrorLabel()];

            }

            // 事务提交
            $transaction->commit();

            $callback = url('home/api/show', ['id' => $api->encode_id]);

            return ['status' => 'success', 'message' => '编辑成功', 'callback' => $callback];

        }

        return $this->display('create', ['api' => $api, 'module' => $api->module]);

    }

    /**
     * 编辑字段
     * @param $id
     * @return string
     */
    public function actionField($id)
    {

        $request = Yii::$app->request;

        $api = StoreApi::findModel(['encode_id' => $id]);

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();

            $api->scenario = 'update';

            if(!$api->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if(!$api->store()){

                $transaction->rollBack();
                return ['status' => 'error', 'message' => $api->getErrorMessage(), 'label' => $api->getErrorLabel()];

            }

            // 事务提交
            $transaction->commit();

            return ['status' => 'success', 'message' => '编辑成功'];

        }

        $project = $api->module->project;

        $project->current_version = $api->module->version;

        return $this->display('/home/field/create', ['api' => $api, 'project' => $project]);
    }

    /**
     * 删除接口
     * @param $id
     * @return array|string
     */
    public function actionDelete($id)
    {

        $request = Yii::$app->request;

        $api = DeleteApi::findModel(['encode_id' => $id]);

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            if(!$api->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($api->delete()) {

                $callback = url('home/project/show', ['version_id' => $api->module->version->encode_id]);

                return ['status' => 'success', 'message' => '删除成功', 'callback' => $callback];

            }

            return ['status' => 'error', 'message' => $api->getErrorMessage(), 'label' => $api->getErrorLabel()];

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

        $project = $api->module->project;

        // 获取当前版本
        $project->current_version = $api->module->version;

        switch ($tab) {
            case 'home':
                $view  = '/home/api/home';
                break;
            case 'field':
                $view  = '/home/field/home';
                break;
            case 'debug':
                $view  = '/home/api/debug';
                break;
            default:
                $view  = '/home/api/home';
                break;
        }

        return $this->display($view, ['project' => $project, 'api' => $api]);

    }
}
