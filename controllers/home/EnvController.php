<?php
namespace app\controllers\home;

use app\models\env\DeleteEnv;
use app\models\env\StoreEnv;
use app\models\Member;
use app\models\Project;
use app\models\User;
use app\models\Version;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Site controller
 */
class EnvController extends PublicController
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
     * 创建环境
     * @param $project_id
     * @return array|string
     */
    public function actionCreate($project_id)
    {

        $request  = Yii::$app->request;
        $response = Yii::$app->response;

        $env  = StoreEnv::findModel();

        $env->project_id = $project_id;

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            $env->scenario = 'create';

            if(!$env->load($request->post())){

                return ['code' => 302, 'msg' => '加载数据失败'];

            }

            if ($env->store()) {

                return ['code' => 200, 'msg' => '添加成功'];

            }else{

                return ['code' => 300, 'msg' => $env->getError()];

            }

        }

        return $this->display('create', ['env' => $env]);

    }

    /**
     * 编辑环境
     * @param $id
     * @return array|string
     */
    public function actionUpdate($id)
    {

        $request  = Yii::$app->request;
        $response = Yii::$app->response;

        $env  = StoreEnv::findModel($id);

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            $env->scenario = 'update';

            if(!$env->load($request->post())){

                return ['code' => 302, 'msg' => '加载数据失败'];

            }

            if ($env->store()) {

                return ['code' => 200, 'msg' => '编辑成功'];

            }else{

                return ['code' => 300, 'msg' => $env->getError()];

            }

        }

        return $this->display('create', ['env' => $env]);

    }


    public function actionDelete($id)
    {

        $request  = Yii::$app->request;
        $response = Yii::$app->response;

        $model  = DeleteEnv::findModel($id);

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            if ($model->delete()) {

                return ['code' => 200, 'msg' => '删除成功'];

            }else{

                return ['code' => 300, 'msg' => $model->getError()];

            }

        }

        return $this->display('delete');

    }


}
