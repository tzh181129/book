<?php
/**
 * Created by PhpStorm.
 * User: 罗阁科技
 * Date: 2018/10/26
 * Time: 18:19
 */

namespace app\admin\controller;

use app\tplay\lib\Base;
use think\Db;
use think\db\Query;
use think\helper\Arr;
use think\Request;
use app\common\model\Feedback as FeedbackModel;

class Feedback extends Base
{
    public function index()
    {
        /** @var Request $request */
        $request = $this->request;
        $query = new FeedbackModel();

        if ($request->has('status')) {
            $query->where('status', $request->param('status'));
        }
        $data = $query->paginate(15);

        return $this->showList($data);
    }

    public function status($ids, $status)
    {
        if (is_array($ids)) {
            Db::table('lg_feedback')->whereIn('id', $ids)->setField('status', $status);
        } else {
            FeedbackModel::where('id', $ids)->update(['status' => $status]);
        }

        return $this->success('操作成功');
    }

    public function deletes($ids)
    {
        $ids2 = [];
        foreach ($ids as $id) {
            $ids2[] = (int)$id;
        }
        FeedbackModel::destroy($ids2);
        return $this->success('删除成功');
    }
}