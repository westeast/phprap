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
                        'actions' => ['create', 'update', 'select','delete', 'show'],
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
        $response = Yii::$app->response;

        $version  = Version::findModel($version_id);

        $module   = StoreModule::findModel();

        $module->version_id = $version->id;
        $module->project_id = $version->project_id;

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            $module->scenario = 'create';

            if(!$module->load($request->post())){

                return ['code' => 302, 'msg' => '加载数据失败'];

            }

            if ($module->store()) {

                return ['code' => 200, 'msg' => '创建成功'];

            }else{

                return ['code' => 300, 'msg' => $module->getError()];

            }

        }

        return $this->display('create', ['module' => $module]);

    }

    /**
     * 更新模板
     * @param $id
     * @return array|string
     */
    public function actionUpdate($id)
    {

        $request  = Yii::$app->request;
        $response = Yii::$app->response;

        $module = StoreModule::findModel($id);

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            $module->scenario = 'update';

            if(!$module->load($request->post())){

                return ['code' => 302, 'msg' => '加载数据失败'];

            }

            if ($module->store()) {

                return ['code' => 200, 'msg' => '编辑成功'];

            }else{

                return ['code' => 300, 'field' => $module->field,'msg' => $module->error];

            }

        }

        return $this->display('create', ['module' => $module]);

    }

    public function actionDelete($id)
    {

        $request  = Yii::$app->request;
        $response = Yii::$app->response;

        $module   = DeleteModule::findModel($id);

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            if(!$module->load($request->post())){

                return ['code' => 302, 'msg' => '加载数据失败'];

            }

            if ($module->delete()) {

                return ['code' => 200, 'msg' => '删除成功'];

            }else{

                return ['code' => 300, 'msg' => $module->getError()];

            }

        }

        return $this->display('delete', ['module' => $module]);

    }


}
