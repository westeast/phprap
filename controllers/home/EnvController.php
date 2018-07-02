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

    /**
     * 创建环境
     * @param $project_id
     * @return array|string
     */
    public function actionCreate($project_id)
    {

        $request  = Yii::$app->request;

        $project  = Project::findModel(['encode_id' => $project_id]);
        $env      = StoreEnv::findModel();

        $env->project_id = $project->id;

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            $env->scenario = 'create';

            if(!$env->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($env->store()) {

                return ['status' => 'success', 'message' => '创建成功'];

            }

            return ['status' => 'error', 'message' => $env->getErrorMessage(), 'label' => $env->getErrorLabel()];

        }

        return $this->display('create', ['env' => $env]);

    }

    /**
     * 更新环境
     * @param $id
     * @return array|string
     */
    public function actionUpdate($id)
    {

        $request = Yii::$app->request;

        $env     = StoreEnv::findModel(['encode_id' => $id]);

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            $env->scenario = 'update';

            if(!$env->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($env->store()) {

                return ['status' => 'success', 'message' => '编辑成功'];

            }

            return ['status' => 'error', 'message' => $env->getErrorMessage(), 'label' => $env->getErrorLabel()];

        }

        return $this->display('create', ['env' => $env]);

    }

    /**
     * 删除环境
     * @param $id
     * @return array|string
     */
    public function actionDelete($id)
    {

        $request = Yii::$app->request;

        $env     = DeleteEnv::findModel(['encode_id' => $id]);

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($env->delete()) {

                return ['status' => 'success', 'message' => '删除成功'];

            }

            return ['status' => 'error', 'message' => $env->getErrorMessage(), 'label' => $env->getErrorLabel()];

        }

        return $this->display('delete');

    }

}
