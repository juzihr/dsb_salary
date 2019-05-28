<?php
namespace app\dsb\controller;
use app\dsb\model\Employee as EmployeeModel;
use \think\Request;
class Employee extends Common {
    public function index() {
        $search = input('search');
        $status = input('status', 0);
        $where = ['company_id'=>$this->company['id']];
        $employee_count['total'] = EmployeeModel::where($where)->count();
        $employee_count['in'] = EmployeeModel::where(array_merge($where, ['status'=>1]))->count();
        $employee_count['out'] = EmployeeModel::where(array_merge($where, ['status'=>2]))->count();
        if ($search) {
            $employee_list = EmployeeModel::where("`company_id` = {$this->company['id']} AND `real_name` like '%{$search}%' OR `idcard` like '%{$search}%' OR `mobile` like '%{$search}%' OR `email` like '%{$search}%'")->paginate(20);
        }elseif ($status) {
            $employee_list = EmployeeModel::where(array_merge($where, ['status'=>$status]))->paginate(20);
        }else{
            $employee_list = EmployeeModel::where($where)->paginate(20);
        }
        $this->assign('search', $search);
        $this->assign('status', $status);
        $this->assign('employee_count', $employee_count);
        $this->assign('employee_list', $employee_list);
        return view();
    }

    public function add() {
        if (Request::instance()->isAjax()) {
            try {
                $real_name = input('real_name');
                $idcard = strtoupper(input('idcard'));
                $mobile = input('mobile');
                $email = input('email');
                if ( !is_idcard($idcard)) throw new \Exception('身份证格式错误', 400);
                if ($mobile && !is_mobile($mobile)) throw new \Exception('手机号格式错误', 400);
                if ($email && !is_email($email)) throw new \Exception('邮箱格式错误', 400);
                $count = EmployeeModel::where(['company_id'=>$this->company['id'], 'idcard'=>$idcard])->count();
                if ($count) throw new \Exception('员工信息已存在', 400);
                EmployeeModel::create([
                    'created_at' => time(),
                    'updated_at' => time(),
                    'company_id' => $this->company->id,
                    'real_name' => $real_name,
                    'idcard' => $idcard,
                    'mobile' => $mobile,
                    'email' => $email,
                    'status' => 1,
                ]);
                die(json_encode(['code'=>200,'msg'=>'员工添加成功']));
            }catch (\Exception $e){
                die(json_encode(['code'=>$e->getCode(), 'msg'=>$e->getMessage()]));
            }
        }
    }

    public function edit() {
        if (Request::instance()->isAjax()) {
            try {
                $id = input('id');
                $real_name = input('real_name');
                $idcard = input('idcard');
                $mobile = input('mobile');
                $email = input('email');
                $status = input('status');
                $employee = EmployeeModel::where(['id'=>$id])->find();
                if ( !$employee) throw new \Exception('员工信息不存在', 400);
                $count = EmployeeModel::where(['company_id'=>$this->company['id'], 'idcard'=>$idcard])->count();
                EmployeeModel::where(['id'=>$id])->update(['real_name'=>$real_name, 'idcard'=>$idcard, 'mobile'=>$mobile, 'email'=>$email, 'status'=>$status]);
                die(json_encode(['code'=>200, 'msg'=>'员工修改成功']));
            }catch (\Exception $e) {
                die(json_encode(['code'=>$e->getCode(), 'msg'=>$e->getMessage()]));
            }
        }
    }
}