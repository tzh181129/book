<?php
// +----------------------------------------------------------------------
// | Tplay [ WE ONLY DO WHAT IS NECESSARY ]
// +----------------------------------------------------------------------
// | Copyright (c) 2018 http://tplay.pengyichen.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 听雨 < 389625819@qq.com >
// +----------------------------------------------------------------------

namespace app\index\controller;

use think\Config;
use think\Session;
use think\Cookie;
use think\Db;
use \think\Validate;

class User extends \think\Controller
{
    public function qqLogin(){

        if(Cookie::has('user')) {
          $user = Db::name('user')->where(['login_string'=>Cookie::get('user')])->find();
          if(empty($user)){
            Cookie::delete('user');
          } else {
            $this->redirect('index/index/index');
          }
        }

        //把第一次访问之前的url存入Session待用
        if(!Session::has('user_url')){
            Session::set('user_url',$this->request->param('url'));
        }

        $config = Config::get('thirdlogin.qq');
        // 获取回调地址
        $url = $config['callback'];
        $redirect_uri = urlencode($url);
        
        $appid = $config['appid'];
        $appsecret = $config['appsecret'];
        
        Session::set('state',md5(uniqid(rand(), TRUE)));
         // 获取code码，用于和QQ服务器申请token。 注：依据OAuth2.0要求，此处授权登录需要用户端操作
         if(empty($this->request->param('code')) && !Session::has('code')){
           //以下信息可安放在用户登录界面上：
          $url= 'https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id='.$appid.'&redirect_uri='.$redirect_uri.'&scope=get_user_info&state='.Session::get('state');
          header('Location:'.$url);//跳转到第三方登录入口
          exit;
         }

         // 依据code码去获取openid和access_token，自己的后台服务器直接向QQ服务器申请即可
         if (!empty($this->request->param('code')) && !Session::has('token')){

            Session::set('code',$this->request->param('code'));

            $url="https://graph.qq.com/oauth2.0/token?client_id=".$appid.
             "&client_secret=".$appsecret."&code=".Session::get('code')."&grant_type=authorization_code"
             ."&state=".Session::get('state')."&redirect_uri=".$redirect_uri;
            $res = $this->https_request($url);
            $res = explode("&",$res);
            $token = [];
            foreach ($res as $k => $val) {
              $val = explode("=",$val);
              $token[$val[0]] = $val[1];
            }
         }

         // 依据申请到的access_token和openid，申请Userinfo信息。
         if (isset($token['access_token'])){
          $url = "https://graph.qq.com/oauth2.0/me?access_token=".$token['access_token'];
          $openid = $this->https_request($url);
          if (preg_match('/\"openid\":\"(\w+)\"/i', $openid, $match)) { 
            $openid = $match[1]; 
          }

          $url = "https://graph.qq.com/user/get_user_info?oauth_consumer_key=".$appid."&access_token=".$token['access_token']."&openid=".$openid.'&format=json';
          
          $userinfo = $this->https_request($url);
          $userinfo = json_decode($userinfo,true);


          Session::delete('code');
          Session::delete('state');

          $user_url = Session::get('user_url');
          Session::delete('user_url');

          if(!empty($userinfo)){
              $user = Db::name('user')->where(['openid'=>$openid])->find();
              $salt = salt(create_code(20));

              if(empty($user)) {

                if($user['status'] == -1) {
                  return $this->error('你已被拉黑');
                }

                $data = [
                  'name' => $userinfo['nickname'],
                  'openid' => $openid,
                  'gender' =>$userinfo['gender'],
                  'thumb' => $userinfo['figureurl_qq_1'],
                  'login_string' => $salt,
                  'create_time' => time(),
                  'last_login_time' => time(),
                  'last_login_ip' => $this->request->ip(),
                  'login_num' => 1,
                ];
                if(false == Db::name('user')->insert($data)){
                  return $this->error('登录失败');
                }
              } else {

                if(false == Db::name('user')->where('id',$user['id'])->update(['login_string'=>$salt,'last_login_time'=>time(),'last_login_ip'=>$this->request->ip(),'login_num'=>$user['login_num']+1])) {
                  return $this->error('登录失败');
                }
              }

              Cookie::set('user',$salt,36000);
              $this->redirect($user_url);
          } else {

            return $this->error('未知错误');
          }
       }
    }

   // cURL函数简单封装
   protected function https_request($url, $data = null)
   {
      $curl = curl_init();
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
      if (!empty($data)){
       curl_setopt($curl, CURLOPT_POST, 1);
       curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
      }
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      $output = curl_exec($curl);
      curl_close($curl);
      return $output;
   }


   public function messages()
   {
      if($this->request->isPost()){
          $post = $this->request->post();
          $validate = new \think\Validate([
              'message'  => 'require|length:1,120',
              'content_id' => 'require|length:1,11|number',
          ]);

          if (!$validate->check($post)) {
            return $this->error($validate->getError());
          }

          $post['message'] = $this->ubbReplace($post['message']);

          if(preg_match("/@(.*)\s/",$post['message'],$reply)) {
            $user = Db::name('user')->where(['status'=>1,'name'=>$reply[1]])->find();
            if(empty($user)) {
              return $this->error('你@的用户不存在');
            }
            $post['to_user_id'] = $user['id'];
            $post['message'] = preg_replace ( "/@(.*) /", "",$post['message']);
            if(empty($post['message'])){
              return $this->error('请填写评论内容');
            } 
            $post['message'] = "<span style='color:#1E9FFF'> @".$user['name']." </span>".$post['message'];
          }

          $post['message'] = preg_replace ( "[\[em_([0-9]*)\]]", "<img src=\"/static/public/qqFace/arclist/$1.gif\" />", $post['message']);
          
          if(Cookie::has('user')) {
              $user = Db::name('user')->where(['login_string'=>Cookie::get('user')])->find();
              if(empty($user)){
                  Cookie::delete('user');
                  return $this->error('登录已超时,重新登录');
              } else {
                  
                  if(!empty(Db::name('message')->where(['user_id'=>$user['id'],'create_time'=>['>=',time()-60]])->find())){
                    return $this->error('60秒内只能提交一次发言');
                  }

                  $post['create_time'] = time();
                  $post['ip'] = $this->request->ip();
                  $post['user_id'] = $user['id'];
                  if(false ==  \think\Loader::model("common/message")->allowField(true)->save($post)){
                      return $this->error('提交失败');
                  }

                  $data = [
                    'id' => \think\Loader::model("common/message")->id,
                    'name' => $user['name'],
                    'create_time' => date('Y-m-d H:i:s',time()),
                    'thumb' => $user['thumb'],
                    'message' => $post['message']
                  ];
                  $data = json_encode($data);
                  return $data;
              }
          } else {
            return $this->error('请先登录');
          }

      } else {
          return $this->error('非法请求');
      }
   }


   protected function ubbReplace($str) {
      $str = str_replace ( ">", '<；', $str );    
      $str = str_replace ( ">", '>；', $str );
      $str = str_replace ( "\n", '>；br/>；', $str ); 
      //$str = preg_replace ( "[\[em_([0-9]*)\]]", "<img src=\"/static/public/qqFace/arclist/$1.gif\" />", $str );
      return $str;
  }

  public function login()
  {
    if($this->request->isPost()){
      $post = $this->request->post();
      $validate = new \think\Validate([
          'username'  => 'require',
          'password' => 'require',
          'captcha|验证码'=>'require|captcha'
      ]);
      if (!$validate->check($post)) {
          return $this->error($validate->getError());
      }
      $user = Db::name('user')->where(['username'=>$post['username']])->find();

      if(!empty($user)) {
        if($user['password'] !== md5(md5($post['password']).$user['salt'])){
          return $this->error('用户名或密码错误');
        }
        if($user['status'] == -1) {
          return $this->error('你已被拉黑');
        }

        $login_string = salt();
        if(false == Db::name('user')->where(['id'=>$user['id']])->update(['login_string'=>$login_string,'login_num'=>$user['login_num']+1,'last_login_time'=>time()])){
          return $this->error('登录失败');
        }
      } else {
         return $this->error('用户名不存在');
      }
      Cookie::set('user',$login_string,36000);
      return $this->success('登录成功','/index/index/index');
    } else {
      return $this->fetch();
    }
  }

  public function registered()
  {
    if($this->request->isPost()){
      $post = $this->request->post();
      $validate = new \think\Validate([
          'username'  => 'require',
          'password' => 'require|confirm',
          'name' => 'require',
          'captcha|验证码'=>'require|captcha'
      ]);
      if (!$validate->check($post)) {
          return $this->error($validate->getError());
      }
      if(!empty(Db::name('user')->where(['username'=>$post['username']])->find())) {
          return $this->error('用户名已被占用');
      }

      $post['salt'] = salt();
      $post['password'] = md5(md5($post['password']).$post['salt']);

      if(false == \think\loader::model('common/user')->allowField(true)->save($post)) {
          return $this->error('注册失败');
      }
      return $this->success('注册成功','/index/index/index');
    } else {
      return $this->fetch();
    }
  }

  public function logout($value='')
  {
    if(!Cookie::has('user')){
      return $this->error('非法请求');
    }
    Cookie::delete('user');
    $this->redirect('/index/index/index');
  }
}
