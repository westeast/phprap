<?php
namespace app\controllers\home;

use app\models\apply\SearchApply;
use app\models\apply\StoreApply;
use app\models\Project;
use app\models\User;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Response;

/**
 * Site controller
 */
class ApplyController extends PublicController
{

    public function actionIndex()
    {

        $params = Yii::$app->params;

        $user = Yii::$app->user->identity;

        $params['project_ids'] = $user->getMyCreatedProjects()->select('id');

        $model = SearchApply::findModel()->search($params);

        return $this->display('index', ['model' => $model]);
    }

    /**
     * 添加申请
     * @return string
     */
    public function actionCreate($project_id)
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $apply =  StoreApply::findModel();

        $project = Project::findModel(['encode_id' => $project_id]);

        if(!Yii::$app->user->identity->id){

            $callback = url('home/project/search');
            $callback = url('home/account/login', ['callback' => $callback]);

            return ['status' => 'nologin', 'message' => '请先登录后再申请', 'callback' => $callback];

        }

        if(!$project || $project->type == $project::PRIVATE_TYPE){

            return ['status' => 'error', 'message' => '私有项目不允许申请'];

        }

        $apply->project_id = $project->id;

        if ($apply->store()) {

            return ['status' => 'success', 'message' => '申请成功'];

        }

        return ['status' => 'error', 'message' => $apply->getErrorMessage(), 'label' => $apply->getErrorLabel()];

    }

}
