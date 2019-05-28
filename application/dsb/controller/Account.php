<?php
namespace app\dsb\controller;
use app\dsb\model\Employee as EmployeeModel;
use app\dsb\model\Detail as DetailModel;
use app\dsb\model\Salary as SalaryModel;
use \think\Request;
use think\Controller;
class Account extends Controller{
    private $account = 0.00;
    private $salary_list = [];
    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $month = input('month');
        $batch = input('batch');
        $employee = session('employee');
        if ($employee) {
            $month_list = $batch_list = [];
            $salary_list = SalaryModel::field('month')->where(['employee_id'=>$employee['id']])->order('month desc')->select();
            foreach ($salary_list as $salary) {
                if ( !in_array($salary['month'], $month_list)) $month_list[] = $salary['month'];
            }
            if ($month_list) {
                if ( !in_array($month, $month_list)) $month = $month_list[0];
                $salary_list = SalaryModel::field('batch')->where(['employee_id'=>$employee['id'],'month'=>$month])->order('batch desc')->select();
                foreach ($salary_list as $salary) {
                    if ( !in_array($salary['batch'], $batch_list)) $batch_list[] = $salary['batch'];
                }
                if ( !in_array($batch, $batch_list)) $batch = $batch_list[0];
                $detail_name = [];
                $detail_list = DetailModel::field('id,detail_name')->select();
                foreach ($detail_list as $k => $detail) {
                    $detail_name[$detail['id']] = $detail['detail_name'];
                }
                $index = 0;
                $salary_list = SalaryModel::where(['employee_id'=>$employee['id'],'month'=>$month,'batch'=>$batch])->order('sort asc')->select();
                foreach ($salary_list as $salary) {
                    if (strpos($detail_name[$salary['did']], '实发工资') !== false) {
                        $this->account = $salary['value'];
                    }
                    if ($salary['cid']) {
                        if ( !$index) $index = $salary['sort'];
                        $this->salary_list[$index]['detail_name'] = $detail_name[$salary['cid']];
                        $this->salary_list[$index]['value'] = '';
                        $this->salary_list[$index]['list'][$salary['sort']] = ['detail_name'=>$detail_name[$salary['did']], 'value'=>$salary['value'], 'list'=>[]];
                    }else{
                        $index = 0;
                        $this->salary_list[] = ['detail_name'=>$detail_name[$salary['did']], 'value'=>$salary['value'], 'list'=>[]];
                    }
                }
                $this->assign('month', $month);
                $this->assign('batch', $batch);
                $this->assign('employee', $employee);
                $this->assign('month_list', $month_list);
                $this->assign('batch_list', $batch_list);
                $this->assign('account', $this->account);
                $this->assign('salary_list', $this->salary_list);
                return view();
            }else{
                return view('empty');
            }
        }else{
            $this->redirect("/dsb/account/login/month/$month/batch/$batch.html");
        }
    }

    public function login() {
        $month = input('month');
        $batch = input('batch');
        if (Request::instance()->isPost()) {
            $mobile = input('mobile');
            $code = input('code');
            $verify = session('verify');
            if ( !$code || !$verify || $mobile != $verify['mobile'] || $code != $verify['code']) {
                $this->assign('error_msg', '验证码错误');
            }else{
                $employee = EmployeeModel::where(['mobile'=>$mobile])->order('id desc')->find();
                if ($employee) {
                    session('employee', $employee);
                    $this->redirect("/dsb/account/index/month/$month/batch/$batch.html");
                }else{
                    $this->assign('error_msg', '手机号不存在');
                }
            }
        }
        $this->assign('month', $month);
        $this->assign('batch', $batch);
        return view();
    }

    public function logout() {
        session('employee', null);
        $this->redirect('/dsb/account/login.html');
    }
    
}