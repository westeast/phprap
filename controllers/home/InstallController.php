<?php

namespace app\controllers\home;

use app\models\Project;
use app\models\project\StoreProject;
use Yii;
use yii\db\Exception;
use yii\web\Response;
use app\models\User;

class InstallController extends PublicController
{

    public $tables = [];

    public function beforeAction($action)
    {

        if($this->isInstalled()){
            exit('PHPRAP已安装过，请不要重复安装');
        }

        return true;
    }

    /**
     * 安装步骤一，环境检测
     * @return array|string
     */
    public function actionStep1()
    {

        $request  = Yii::$app->request;
        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            Yii::$app->cache->set('step', 1);

            return ['status' => 'success', 'callback' => url('home/install/step2')];

        }

        $chmods = [
            'runtime' => [
                'all_chmod' => $this->get_chmod(Yii::getAlias("@runtime")),
                'is_writable' => is_writable(Yii::getAlias("@runtime")),
            ],
            'runtime/install' => [
                'all_chmod' => $this->get_chmod(Yii::getAlias("@runtime") . '/install'),
                'is_writable' => is_writable(Yii::getAlias("@runtime") . '/install'),
            ],
            'configs/db.php' => [
                'all_chmod' => $this->get_chmod(Yii::getAlias("@app") . '/configs/db.php'),
                'is_writable' => is_writable(Yii::getAlias("@app") . '/configs/db.php'),
            ],
        ];

        return $this->display('/install/step1', ['chmods' => $chmods]);
    }

    /**
     * 安装步骤二，初始化数据库并将数据库信息写入配置文件
     * @return array|string|\yii\web\Response
     */
    public function actionStep2()
    {

        $request  = Yii::$app->request;

        if(Yii::$app->cache->get('step') != 1){

            return $this->redirect(['home/install/step1']);

        }

        if($request->isPost){

            Yii::$app->response->format = Response::FORMAT_JSON;

            $step2 = $request->post('Step2');

            $db = [
                'dsn'   => "mysql:host={$step2['host']};port={$step2['port']};dbname={$step2['dbname']}",
                'username' => $step2['username'],
                'password' => $step2['password'],
                'charset'  => 'utf8',
                'tablePrefix' => $step2['prefix'],

                'enableSchemaCache' => true,
                'schemaCacheDuration' => 60,
                'schemaCache' => 'cache',
            ];

            $connection = new \yii\db\Connection($db);

            // 判断数据库连接状态
            try {

                $connection->open();

            } catch(Exception $e) {

                return ['status' => 'error', 'message' => '数据库连接失败，请检查数据库配置信息是否正确'];
            }

            if($connection->isActive){

                // 读取初始化数据库脚本文件内容
                $lines = file(Yii::getAlias("@runtime") .'/install/db.sql');

                $sql = "";

                // 循环排除掉不合法的sql语句
                foreach($lines as $line){

                    $line = trim($line);

                    if($line != ""){

                        if(!($line{0} == "#" || $line{0}.$line{1} == "--")){

                            $sql .= trim($line);
                        }
                    }
                }
                // 将初始sql文件里的表前缀替换成表单输入的自定义前缀
                $sql = str_replace('doc_', $step2['prefix'], $sql);

                // 执行多条sql语句
                $result = $connection->createCommand($sql)->execute();

                if($result >= 0){

                    // 将数据库信息写入配置文件
                    $db = ['class' => 'yii\db\Connection'] + $db;

                    $config = "<?php\r\nreturn\n" . var_export($db,true) . "\r\n?>";

                    if(file_put_contents(Yii::getAlias("@app") . '/configs/db.php', $config) === false){

                        return ['status' => 'error', 'message' => '数据库配置文件写入错误，请检查configs文件夹是否有可写权限'];

                    }

                    Yii::$app->cache->set('step', 2);

                    return ['status' => 'success', 'callback' => url('home/install/step3')];

                }

                return ['status' => 'error', 'message' => '数据库初始化安装失败，请检查runtime/install/db.sql文件是否完整'];

            }

        }

        return $this->display('/install/step2');

    }

    /**
     * 安装步骤三，创建总管理员
     * @return string|\yii\web\Response
     */
    public function actionStep3()
    {

        $request = Yii::$app->request;

        if(Yii::$app->cache->get('step') != 2){

            return $this->redirect(['home/install/step2']);

        }

        if($request->isPost){

            // 开启事务
            $transaction = Yii::$app->db->beginTransaction();

            Yii::$app->response->format = Response::FORMAT_JSON;

            $step3 = $request->post('Step3');

            $user = new User();

            $user->name = $step3['name'];
            $user->email = $step3['email'];
            $user->ip = Yii::$app->request->userIP;
            $user->location = $user->getLocation();

            $user->setPassword($step3['password']);
            $user->generateAuthKey();

            $user->type = $user::ADMIN_TYPE;

            if(!$user->save()){
                $transaction->rollBack();
                return ['status' => 'error', 'message' => $user->getErrorMessage()];
            }

            $project = StoreProject::find()->one();

            $project->creater_id = $user->id;

            if(!$project->store()){
                $transaction->rollBack();
                return ['status' => 'error', 'message' => $project->getErrorMessage()];
            }

            Yii::$app->user->login($user);

            Yii::$app->cache->set('step', 3);

            // 事务提交
            $transaction->commit();

            return ['status' => 'success', 'callback' => url('home/install/step4')];

        }

        return $this->display('/install/step3');

    }

    /**
     * 安装步骤四
     * @return string|\yii\web\Response
     */
    public function actionStep4()
    {

        if(Yii::$app->cache->get('step') != 3){

            return $this->redirect(['home/install/step3']);

        }

        // 获取所有数据表
        $_sql = file_get_contents(Yii::getAlias("@runtime") .'/install/db.sql');
        $_arr = array_filter(explode(';', $_sql));

        foreach ($_arr as $k => $v) {

            $sql = str_replace('doc_', Yii::$app->db->tablePrefix, trim($v));

            if($table = explode('EXISTS', $sql)[1]){

                $tables[] = $table;

            }

        }

        // 创建安装锁文件
        if(file_put_contents(Yii::getAlias("@runtime") . '/install/install.lock', json_encode(['installed_at' => date('Y-m-d H:i:s')])) === false){

            return ['status' => 'error', 'message' => '数据库锁文件写入错误，请检查runtime/install文件夹是否有可写权限'];

        }

        Yii::$app->cache->set('step', 4);

        return $this->display('/install/step4', ['tables' => $tables]);

    }

    // 获取权限
    private function get_chmod($dirName){

        if (is_readable ($dirName)) {

            $chmod = '可读,';

        }

        if (is_writable ($dirName)) {

            $chmod .= '可写,';

        }

        if (is_executable ($dirName)) {

            $chmod .= '可执行,';

        }

        return trim($chmod, ',');

    }

}
