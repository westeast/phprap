<?php
/**
 * 静态资源路径 function.STATIC_PATH.php
 * Author: 勾国印 (phper@gouguoyin.cn)
 * Date: 2015年11月2日 下午3:58:56
*/

function smarty_function_STATIC_VERSION($params, &$smarty)
{

    return Yii::$app->params['app_version'];
    
}
