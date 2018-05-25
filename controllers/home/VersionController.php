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

                return ['code' => 'error', 'msg' => '加载数据失败'];

            }

            if ($version->store()) {

                return ['code' => 'success', 'msg' => '创建成功'];

            }else{

                return ['code' => 'error', 'msg' => $version->getError()];

            }

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

                return ['code' => 'error', 'msg' => '加载数据失败'];

            }

            if ($version->store()) {

                return ['code' => 'success', 'msg' => '编辑成功'];

            }else{

                return ['code' => 'error', 'label' => $version->getLabel(),'msg' => $version->getError()];

            }

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
        $response = Yii::$app->response;

        $version  = DeleteVersion::findModel($id);

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            if(!$version->load($request->post())){

                return ['code' => 302, 'msg' => '加载数据失败'];

            }

            if ($version->delete()) {

                return ['code' => 200, 'msg' => '删除成功'];

            }else{

                return ['code' => 300, 'msg' => $version->getError()];

            }

        }

        return $this->display('delete', ['version' => $version]);

    }

}
