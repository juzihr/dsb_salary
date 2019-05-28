<?php
namespace app\dsb\controller;
require_once EXTEND_PATH . '/gt3/lib/class.geetestlib.php';
use app\dsb\model\User as UserModel;
use app\dsb\model\Company as CompanyModel;
use app\dsb\model\Sms as SmsModel;
use gt3\GeetestLib;        
use think\Controller;
use think\Request;
class User extends Controller {
    public function __construct() {
        parent::__construct();
        $this->geetest = new GeetestLib(); 
    }

    public function stat() {
        $data = [
            'user_id' => mt_rand(1000, 9999),
            'client_type' => 'web',
            'ip_address' => get_client_ip()
        ];
        $status = $this->geetest->pre_process($data, 1);
        session('gtserver', $status);
        session('user_id', $data['user_id']);
        die($this->geetest->get_response_str());
    }

    public function verify() {
        try{
            $data = [
                'user_id' => session('user_id'), 
                'client_type' => 'web',
                'ip_address' => get_client_ip()
            ];
            $result = $this->geetest->success_validate(input('geetest_challenge'), input('geetest_validate'), input('geetest_seccode'), $data);
            if ( !$result) throw new \Exception('非法请求', 400);

            $mobile = input('mobile');
            $verify = session('verify');
            if ( !is_mobile($mobile)) throw new \Exception('手机号格式错误', 400);
            if ($verify && $mobile == $verify['mobile'] && $verify['created_at'] > time() - 60) throw new \Exception('验证码一分钟之内不能重复发送', 400);
            $code = mt_rand(1000, 9999);
            SmsModel::create(['created_at'=>time(), 'updated_at'=>time(), 'mobile'=>$mobile, 'code'=>$code]);
            send_sms($mobile, '您的验证码是: ' . $code);
            session('verify', ['created_at'=>time(), 'mobile'=>$mobile, 'code'=>$code]);
            die(json_encode(['code'=>200, 'msg'=>'ok']));
        }catch (\Exception $e){
            die(json_encode(['code'=>$e->getCode(), 'msg'=>$e->getMessage()]));
        }
    }

    public function register() {
        if (Request::instance()->isAjax()) {
            try{
                $mobile = input('mobile');
                $code = input('code');
                $password = input('password');
                $repeat = input('repeat');
                $verify = session('verify');
                if ( !is_mobile($mobile)) throw new \Exception('手机号格式错误', 400);
                if ( !$verify) throw new \Exception('请发送验证码', 400);
                if ($mobile != $verify['mobile'] || $code != $verify['code']) {
                    throw new \Exception('验证码错误', 400);
                }
                if ($password != $repeat) throw new \Exception('两次输入密码不一致', 400);
                $user = UserModel::where(['mobile'=>$mobile])->find();
                if ($user) throw new \Exception('手机号已存在', 400);
                session('mobile', $mobile);
                session('password', $password);
                die(json_encode(['code'=>200, 'msg'=>'ok']));
            }catch (\Exception $e){
                die(json_encode(['code'=>$e->getCode(), 'msg'=>$e->getMessage()]));
            }    
        }
        return view();
    }

    public function step2() {
        $mobile = session('mobile');
        $password = session('password');
        if ( !$mobile || !$password) $this->redirect('/dsb/user/register.html');
        if (Request::instance()->isAjax()) {
            try {
                $company_name = input('company_name');
                $address = input('address');
                $contact = input('contact');
                $email = input('email');
                if ( !is_email($email)) throw new \Exception('邮箱格式错误', 400);
                $company = CompanyModel::where(['company_name'=>$company_name])->find();
                if ($company) throw new \Exception('公司名称已存在', 400);
                $upload = Request::instance()->file('license')->move('uploads/license/');
                $company = [
                    'created_at' => time(),
                    'updated_at' => time(),
                    'company_name' => $company_name,
                    'address' => $address,
                    'contact' => $contact,
                    'email' => $email,
                    'license' => '/uploads/license/' . $upload->getSaveName(),
                    'status' => 1,
                ];
                $company = CompanyModel::create($company);
                $user = [
                    'created_at' => time(),
                    'updated_at' => time(),
                    'company_id' => $company['id'],
                    'mobile' => $mobile,
                    'password' => md5($password),
                    'status' => 1,
                ];
                session('company', $company);
                session('user', UserModel::create($user));
                die(json_encode(['code'=>200, 'msg'=>'ok']));
            }catch (\Exception $e) {
                die(json_encode(['code'=>$e->getCode(), 'msg'=>$e->getMessage()]));
            }    
        }
        return view();
    }

    public function step3() {
        $company = session('company');
        if ( !$company) $this->redirect('/dsb/user/login.html');
        $this->assign('company', $company);
        return view();
    }

    public function login() {
        try {
            $mobile = input('mobile');
            $password = input('password');
            $user = UserModel::where(['mobile'=>$mobile])->find();
            if ($user) {
                if (md5($password) != $user->password) die(json_encode(['code'=>400, 'msg'=>'用户名或密码错误']));
                session('user', $user);
                session('company', CompanyModel::where(['id'=>$user->company_id])->find());
                die(json_encode(['code'=>200, 'msg'=>'ok', 'data'=>['url'=>'/dsb/home/index.html']]));
            }else{
                die(json_encode(['code'=>300, 'msg'=>'ok', 'data'=>['url'=>'/dsb/user/register.html']]));     
            }
        }catch (\Exception $e) {
            die(json_encode(['code'=>$e->getCode(), 'msg'=>$e->getMessage()]));
        }
    }

    public function logout() {
        session('user', null);
        session('company', null);
        $this->redirect('/dsb/index/index.html');
    }

    public function find() {
        if (Request::instance()->isAjax()) {
            try {
                $mobile = input('mobile');
                $code = input('code');
                $verify = session('verify');
                if ( !is_mobile($mobile)) throw new \Exception('手机号格式错误', 400);
                if ($mobile != $verify['mobile'] || $code != $verify['code']) {
                    throw new \Exception('验证码错误', 400);
                }
                $verify['is_verify'] = true;
                session('verify', $verify);
                die(json_encode(['code'=>200, 'msg'=>'ok']));
            }catch (\Exception $e) {
                die(json_encode(['code'=>$e->getCode(), 'msg'=>$e->getMessage()]));
            }
        }
        return view();
    }

    public function reset() {
        $verify = session('verify');
        if ( !$verify['is_verify']) {
            $this->redirect('/dsb/user/find.html');
        }
        if (Request::instance()->isAjax()) {
            try {
                $password = input('password');
                $repeat = input('repeat');
                if ($password != $repeat) throw new \Exception('两次输入密码不一致', 400);
                UserModel::where(['mobile'=>$verify['mobile']])->update(['updated_at'=>time(), 'password'=>md5($password)]);
                die(json_encode(['code'=>200, 'msg'=>'ok']));
            }catch (\Exception $e) {
                die(json_encode(['code'=>$e->getCode(), 'msg'=>$e->getMessage()]));
            }
        }
        return view();
    }
}
