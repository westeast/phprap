<?php
namespace app\controllers\home;

use app\models\module\DeleteModule;
use app\models\Version;
use app\models\version\DeleteVersion;
use app\models\version\SearchVersion;
use app\models\module\StoreModule;
use app\models\version\StoreVersion;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Site controller
 */
class ModuleController extends PublicController
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],

        ];
    }

    /**
     * 添加模块
     * @return string
     */
    public function actionCreate($version_id)
    {

        $request  = Yii::$app->request;

        $version  = Version::findModel(['encode_id' => $version_id]);

        $module   = StoreModule::findModel();

        $module->version_id = $version->id;
        $module->project_id = $version->project_id;

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            $module->scenario = 'create';

            if(!$module->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($module->store()) {

                return ['status' => 'success', 'message' => '创建成功'];

            }

            return ['status' => 'error', 'model' => $module];

        }

        return $this->display('create', ['module' => $module]);

    }

    /**
     * 更新模块
     * @param $id
     * @return array|string
     */
    public function actionUpdate($id)
    {

        $request  = Yii::$app->request;

        $module = StoreModule::findModel(['encode_id' => $id]);

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            $module->scenario = 'update';

            if(!$module->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($module->store()) {

                return ['status' => 'success', 'message' => '编辑成功'];

            }

            return ['status' => 'error', 'message' => $module->getErrorMessage(), 'label' => $module->getErrorLabel()];

        }

        return $this->display('create', ['module' => $module]);

    }

    /**
     * 删除模块
     * @param $id
     * @return array|string
     */
    public function actionDelete($id)
    {

        $request = Yii::$app->request;

        $module  = DeleteModule::findModel(['encode_id' => $id]);

        if($request->isPost){

            if(!$module->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($module->delete()) {

                return ['status' => 'success', 'message' => '删除成功'];

            }

            return ['status' => 'error', 'message' => $module->getErrorMessage(), 'label' => $module->getErrorLabel()];

        }

        return $this->display('delete', ['module' => $module]);

    }


}
