<?php
namespace app\controllers\home;

use app\models\member\RemoveMember;
use Yii;
use yii\web\Response;
use yii\filters\AccessControl;

use app\models\Project;
use app\models\Member;
use app\models\member\StoreMember;

/**
 * Site controller
 */
class MemberController extends PublicController
{

    /**
     * 添加成员
     * @return string
     */
    public function actionCreate($project_id)
    {

        $request = Yii::$app->request;

        $project = Project::findModel(['encode_id' => $project_id]);
        $member  = StoreMember::findModel();

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            $member->scenario = 'create';

            if(!$member->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            $member->project_id = $project->id;

            if ($member->store()) {

                return ['status' => 'success', 'message' => '添加成功'];

            }

            return ['status' => 'error', 'model' => $member];

        }

        return $this->display('create', ['project' => $project, 'member' => $member]);

    }

    /**
     * 编辑权限
     * @param $id
     * @return array|string
     */
    public function actionUpdate($id)
    {

        $request = Yii::$app->request;

        $member  = StoreMember::findModel(['encode_id' => $id]);

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            $member->scenario = 'update';

            if(!$member->load($request->post())){

                return ['status' => 'error', 'message' => '加载数据失败'];

            }

            if ($member->store()) {

                return ['status' => 'success', 'message' => '编辑成功'];

            }

            return ['status' => 'error', 'model' => $member];

        }

        return $this->display('create', ['member' => $member]);

    }

    /**
     * 查看权限
     * @param $id
     * @return string
     */
    public function actionRule($id)
    {

        $member = StoreMember::findModel(['encode_id' => $id]);

        return $this->display('rule', ['member' => $member]);

    }

    /**
     * 选择成员
     * @return string
     */
    public function actionSelect($project_id, $name)
    {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $project = Project::findModel(['encode_id' => $project_id]);

        $notMembers = $project->getNotMembers(['name' => $name]);

        $user = [];

        foreach ($notMembers as $k => $member){
            $user[$k]['id']   = $member->id;
            $user[$k]['name'] = $member->fullName;
        }

        return $user;

    }

    /**
     * 移除成员
     * @param $id
     * @return array
     */
    public function actionRemove($project_id)
    {

        $request = Yii::$app->request;

        $project = Project::findModel(['encode_id' => $project_id]);

        $member  = RemoveMember::findModel(['project_id' => $project->id]);

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            if ($member->remove()) {

                return ['status' => 'success', 'message' => '移除成功'];

            }

            return ['status' => 'error', 'message' => $member->getErrorMessage(), 'label' => $member->getErrorLabel()];

        }

    }

}
