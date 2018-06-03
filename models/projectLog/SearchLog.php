<?php

namespace app\models\projectLog;

use app\models\ProjectLog;
use Yii;
use yii\data\Pagination;
use app\widgets\LinkPager;

class SearchLog extends ProjectLog
{

    public $pageSize = 20;

    public function search($params = [])
    {

        $this->params = array_map(function ($value) {

            return trim($value);

        }, $params);

        $query = static::find();

        $query->andFilterWhere([
            'project_id'  => $this->params['project_id'],
            'module_id'  => $this->params['module_id'],
            'version_id'  => $this->params['version_id'],
            'api_id'  => $this->params['api_id'],
            'object_name' => $this->params['object_name'],
            'object_id'   => $this->params['object_id'],
        ]);

        $pagination = new Pagination([
            'pageSizeParam' => false,
            'totalCount' => $query->count(),
            'pageSize'   => $this->pageSize,
            'validatePage' => false,
        ]);

        $this->models = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->orderBy('id DESC')
            ->all();

        $this->count = $query->count();

        $this->sql = $query->createCommand()->getRawSql();

//        dump($this->params);

        $this->pages = LinkPager::widget([
            'pagination' => $pagination,
            'nextPageLabel' => '下一页',
            'prevPageLabel' => '上一页',
            'firstPageLabel' => '首页',
            'lastPageLabel' => '尾页',
            'hideOnSinglePage' => true,
            'maxButtonCount' => 5,
        ]);

        return $this;

    }

}
