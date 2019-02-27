<?php
/**
 * Created by PhpStorm.
 * User: 罗阁科技
 * Date: 2018/11/8
 * Time: 12:04
 */

namespace app\common\model;

use think\db\Query;
use think\Model;

class Chat extends Model
{
    protected $table = "lg_user_chat";

    const TYPE_TEXT = 1;//文本消息
    const TYPE_IMAGE = 2;//图片消息
    const TYPE_AUDIO = 3;//语音消息

    const STATUS_READ = 1;//已读
    const STATUS_UNREAD = 0;//未读

    /**
     * @param $query Query
     * @param $user_id
     * @param $chater_id
     */
    public function scopeUsers($query, $user_id, $chater_id)
    {
        $query->where(function ($query) use ($user_id, $chater_id) {
            $query->where('send_uid', $user_id)->where('rec_uid', $chater_id);
        })->whereOr(function ($query) use ($user_id, $chater_id) {
            $query->where('rec_uid', $user_id)->where('send_uid', $chater_id);
        });
    }

    /**
     * chat history with sb,but send by opposite side,which hasn't been read
     * @param $query Query
     * @param $chater_id the opposite chater's id
     * @param $my user's id
     */
    public function scopeNotRead($query, $chater_id, $my)
    {
        $query->where([
            'send_uid' => $chater_id,
            'rec_uid' => $my,
            'is_read' => self::STATUS_UNREAD
        ]);
    }
}