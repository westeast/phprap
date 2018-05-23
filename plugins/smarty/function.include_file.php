<?php
/**
 * 引入模板文件 function.include_file.php
 * Author: 勾国印 (phper@gouguoyin.cn)
 * Date: 2015年11月2日 下午3:58:56
 */

function smarty_function_include_file($params, &$smarty)
{

    // 获取默认视图文件后缀
    $suffix = 'html';

    $file   =  '@app/views/'. $params['name'] . '.' . $suffix;

    $smarty->assign($params);

    return $smarty->fetch($file);

}
