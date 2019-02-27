<?php
/**
 * Created by PhpStorm.
 * User: 罗阁科技
 * Date: 2018/10/26
 * Time: 18:56
 */

namespace app\common\model;

use think\Model;
use traits\model\SoftDelete;

class Feedback extends Model
{
    use SoftDelete;

    protected $table = 'lg_feedback';

    protected $deleteTime = 'delete_at';

    protected $autoWriteTimestamp = 'timestamp';

    protected $createTime = 'created_at';

    protected $updateTime = false;

    protected $type = [
        'images' => 'array',
        'status' => 'boolean'
    ];

    const STATUS_SOLVED = 1;
    const STATUS_UNSOLVED = 0;

    public function scopeSovled($query)
    {
        $query->where('status', self::STATUS_SOLVED);
    }

    public function scopeUnsolved($query)
    {
        $query->where('status', self::STATUS_UNSOLVED);
    }

    public function scopeStauts($query, $status)
    {
        $query->where('status', $status);
    }


}