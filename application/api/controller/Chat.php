<?php
/**
 * Created by PhpStorm.
 * User: 罗阁科技
 * Date: 2018/11/15
 * Time: 11:16
 * 及时聊天控制器
 */

namespace app\api\controller;

use app\common\model\Chat as ChatModel;
use app\common\model\User as UserModel;

class Chat extends BaseController
{
    /**
     * user's chat histories
     * @throws \think\exception\DbException
     */
    public function history($user_id, $chater_id)
    {
        $history = ChatModel::scope('users', $user_id, $chater_id)->select();
        return $this->succeed($history);
    }

    /**
     * chaters with last message and un-read count
     * @param $user_id
     * @throws \think\exception\DbException
     */
    public function chaters($user_id)
    {
        $user = UserModel::get($user_id);
        if ($user) {
            $chaters = $user->chaters()->field('name,thumb')->select();
            foreach ($chaters as $chater) {
                //最后一条记录
                $chater->lastMessage = ChatModel::scope('users', $user_id, $chater->pivot->chater_id)
                    ->order('id', 'desc')->value('message');
                $chater->unRead = ChatModel::scope('notRead', $chater->pivot->chater_id, $user_id)->count();
            }
            return $this->succeed($chaters);
        } else {
            return $this->json([], 404);
        }
    }

    /**
     * change chat's is_read to true
     */
    public function read()
    {
        $params = $this->request->post();

        $res = ChatModel::scope('notRead', $params['chater_id'], $params['user_id'])->update(['is_read' => 1]);

        if ($res) {
            return $this->noContent();
        } else {
            return $this->errors($res);
        }
    }
}