<?php
/**
 * Created by PhpStorm.
 * User: 罗阁科技
 * Date: 2018/10/30
 * Time: 12:51
 */

namespace app\common\model;

//提现
use think\Model;
use traits\model\SoftDelete;

class Withdraw extends Model
{
    use SoftDelete;

    protected $table = 'lg_withdraw';

    protected $createTime = 'created_at';

    protected $deleteTime = 'deleted_at';

    protected $updateTime = 'updated_at';

    protected $autoWriteTimestamp = 'timestamp';

    const STATUS_COMMIT = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_SUCCEED = 2;
    const STATUS_FAILD = 3;

    const WITHDRAW_STATUS = [
        self::STATUS_COMMIT => '发起提现',
        self::STATUS_PROCESSING => '提现中',
        self::STATUS_SUCCEED => '提现成功',
        self::STATUS_FAILD => '提现失败',
    ];

    protected $type = [
        'status' => 'array',
        'oids' => 'array'
    ];
}