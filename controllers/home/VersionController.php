<?php
namespace app\controllers\home;

use app\models\version\DeleteVersion;
use app\models\version\SearchVersion;
use app\models\version\StoreVersion;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Site controller
 */
class VersionController extends PublicController
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
     * 添加成员
     * @return string
     */
    public function actionCreate($project_id)
    {

        $request  = Yii::$app->request;
        $response = Yii::$app->response;

        $version  = StoreVersion::findModel();

        $version->project_id = $project_id;

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            $version->scenario = 'create';

            if(!$version->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($version->store()) {

                return ['status' => 'success', 'message' => '创建成功'];

            }

            return ['status' => 'error', 'model' => $version];

        }

        return $this->display('create', ['version' => $version]);

    }

    public function actionUpdate($id)
    {

        $request  = Yii::$app->request;
        $response = Yii::$app->response;

        $version = StoreVersion::findModel($id);

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            $version->scenario = 'update';

            if(!$version->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($version->store()) {

                return ['status' => 'success', 'message' => '编辑成功'];

            }

            return ['status' => 'error', 'model' => $version];

        }

        return $this->display('create', ['version' => $version]);

    }

    /**
     * 选择版本
     * @return string
     */
    public function actionSelect($project_id, $name)
    {

        $response = Yii::$app->response;
        $response->format = Response::FORMAT_JSON;

        $version = SearchVersion::findModel();

        $version->pageSize = 4;

        $versions = $version->search(['project_id' => $project_id, 'name' =>$name])->models;

        return $versions;

    }

    /**
     * 删除版本
     * @param $id
     * @return array|string
     */
    public function actionDelete($id)
    {

        $request  = Yii::$app->request;

        $version  = DeleteVersion::findModel($id);

        if($request->isPost){

            if(!$version->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($version->delete()) {

                return ['status' => 'success', 'message' => '删除成功'];

            }

            return ['status' => 'error', 'model' => $version];

        }

        return $this->display('delete', ['version' => $version]);

    }

}
