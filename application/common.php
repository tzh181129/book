<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function salt($num = 6)
{
    $str = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
    $salt = substr(str_shuffle($str), 10, $num);
    return $salt;
}

function getSystem($key)
{
    $system = \think\Db::name('system')->where(['key'=>$key,'status'=>['>',0]])->find();
    if($system['type'] == 1){
    	$system['value'] = json_decode($system['value'],true);
    }
    return $system['value'];
}

/**
 * 数组层级缩进转换
 * @param array $array 源数组
 * @param int   $pid
 * @param int   $level
 * @return array
 */
function array2level($array, $pid = 0, $level = 1)
{
    static $list = [];
    foreach ($array as $v) {
        if ($v['pid'] == $pid) {
            $v['level'] = $level;
            $list[]     = $v;
            array2level($array, $v['id'], $level + 1);
        }
    }

    return $list;
}

 function chapter_insert($name, $data)
{
    header("content-type:text/html;charset=UTF-8");
//        $result = $this->daoruuploadFile('admin', 'novel');
//        //添加文本文件
//        //$insert['name'] = $result['name'];
//        $result['path'] = str_replace("\\", "/", $result['data']);
    $formtxt = ROOT_PATH . 'public' . $name;
    $mytxt = fopen($formtxt, "r");
    $content = fread($mytxt, filesize($formtxt));
    fclose($mytxt);
    $txt = mb_convert_encoding($content, "UTF-8", "GBK");
    $pattern = '/第[0-9一二两三四五六七八九十百千万]*[章节]([\w\W]+?)[\r\n]/';          //章节和标题
    $content_pattern = '/第[0-9一二两三四五六七八九十百千万]*[章节]([\w\W]+?)[\r\n]([\w\W]+?)本章完。/';                //内容
    preg_match_all($pattern, $content, $doupou, PREG_PATTERN_ORDER);
    preg_match_all($content_pattern, $content, $doupou_content, PREG_PATTERN_ORDER);
    $insert = [];
    foreach ($doupou_content[2] as $k => $v) {
        $insert['title'] = $doupou[0][$k];
        $insert['content'] = $v;
        $insert['novel_id']=$data['novel_id'];
        $insert['create_time']=time();
        $Novel = new \app\common\model\Chapter;
        $Novel->insert($insert);
    }
    return;
}