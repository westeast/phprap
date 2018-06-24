<?php
namespace app\controllers\admin;

use app\models\account\RegisterForm;
use app\models\account\SearchForm;
use app\models\account\UpdateForm;
use app\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\web\Response;

/**
 * Site controller
 */
class UserController extends PublicController
{

    /**
     * 添加成员
     * @return string
     */
    public function actionIndex()
    {

        $params = Yii::$app->request->queryParams;

        $searchModel = new SearchForm();

        return $this->display('index', ['model' => $searchModel->search($params)]);

    }

    /**
     * 编辑账号
     * @return string
     */
    public function actionUpdate($id)
    {

        $request  = Yii::$app->request;
        $response = Yii::$app->response;

        $user = User::findModel($id);

        $model = new UpdateForm();

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            $model->user_id = $user->id;

            $model->load($request->post());

            if($status = $request->get('status')){

                $user->status = $status;

                if(!$user->save()){

                    return ['status' => 'error', 'message' => $user->getErrorMessage(), 'label' => $user->getErrorLabel()];

                }

            }else{

                if (!$model->store()) {

                    return ['status' => 'error', 'message' => $model->getErrorMessage(), 'label' => $model->getErrorLabel()];

                }
            }

            return ['status' => 'success', 'message' => '操作成功'];


        }

        return $this->display('create', ['user' => $user]);

    }

}
