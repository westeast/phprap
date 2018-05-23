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

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'select','remove'],
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

        $project = Project::findModel($project_id);
        $member  = StoreMember::findModel();

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            $member->scenario = 'create';

            if(!$member->load($request->post())){

                return ['code' => 302, 'msg' => '加载数据失败'];

            }

            $member->project_id = $project_id;

            if ($member->store()) {

                return ['code' => 200, 'msg' => '成员添加成功'];

            }else{

                return ['code' => 300, 'msg' => $member->getError()];

            }

        }

        return $this->display('create', ['project' => $project, 'member' => $member]);

    }

    public function actionUpdate($id)
    {

        $request  = Yii::$app->request;
        $response = Yii::$app->response;

        $member = StoreMember::findModel($id);

        // 获取项目成员外的所有用户

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            $member->scenario = 'update';

            if(!$member->load($request->post())){

                return ['code' => 302, 'msg' => '加载数据失败'];

            }

            if ($member->store()) {

                return ['code' => 200, 'msg' => '编辑成功'];

            }else{

                return ['code' => 300, 'msg' => $member->getError()];

            }

        }

        return $this->display('create', ['member' => $member]);

    }

    /**
     * 选择成员
     * @return string
     */
    public function actionSelect($project_id)
    {

        $request = Yii::$app->request;
        $response = Yii::$app->response;

        $response->format = Response::FORMAT_JSON;

        $project = Project::findModel($project_id);

        $notMembers =  $project->getNotMembers($request->queryParams);

        $user = [];

        foreach ($notMembers as $k => $member){
            $user[$k]['id']   = $member->id;
            $user[$k]['name'] = $member->name . '(' . $member->email . ')';
        }

        return $user;

    }

    /**
     * 移除成员
     * @param $id
     * @return array
     */
    public function actionRemove($id)
    {

        $request  = Yii::$app->request;
        $response = Yii::$app->response;

        $member  = RemoveMember::findModel($id);

        if($request->isPost){

            $response->format = Response::FORMAT_JSON;

            if ($member->remover()) {

                return ['code' => 200, 'msg' => '移除成功'];

            }else{

                return ['code' => 300, 'msg' => $member->getError()];

            }

        }

    }

}
