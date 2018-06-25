<?php
namespace app\controllers\home;

use app\models\apply\SearchApply;
use app\models\apply\StoreApply;
use app\models\member\StoreMember;
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

        $apply = StoreApply::findModel();

        $project = Project::findModel(['encode_id' => $project_id]);

        $apply->project_id = $project->id;

        if ($apply->store()) {

            return ['status' => 'success', 'message' => '申请成功，请耐心等待项目拥有者审核'];

        }

        return ['status' => 'error', 'message' => $apply->getErrorMessage(), 'label' => $apply->getErrorLabel()];

    }

    /**
     * 审核申请
     * @return string
     */
    public function actionCheck($id, $status)
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        // 开启事务
        $transaction = Yii::$app->db->beginTransaction();

        $apply = StoreApply::findModel($id);

        if($apply->status !== $apply::CHECK_STATUS){
            return ['status' => 'error', 'message' => '无效的申请状态'];
        }

        $apply->status = $status;

        if(!$apply->store()){
            $transaction->rollBack();
            return ['status' => 'error', 'message' => $apply->getErrorMessage(), 'label' => $apply->getErrorLabel()];
        }

        // 如果审核通过，向项目成员表插入数据
        if($apply->status == $apply::PASS_STATUS){
            $member = StoreMember::findModel();
            $member->project_id = $apply->project_id;
            $member->user_id = $apply->user_id;

            if(!$member->store()){
                $transaction->rollBack();
                return ['status' => 'error', 'message' => $member->getErrorMessage(), 'label' => $member->getErrorLabel()];
            }
        }

        // 事务提交
        $transaction->commit();

        return ['status' => 'success', 'message' => '操作成功'];

    }

}
