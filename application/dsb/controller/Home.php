<?php
namespace app\dsb\controller;
use app\dsb\model\Employee as EmployeeModel;
class Home extends Common {
    public function index() {
        $start = date('Y-m-01', strtotime(date('Y-m-d')));
        $begin_time = date('m.01', strtotime(date('Y-m-d')));
        $end_time = date('m.d', strtotime("$start +1 month -1 day"));
        $employee_count = EmployeeModel::where(['company_id'=>$this->company->id,'status'=>1])->count();
        $this->assign('begin_time', $begin_time);
        $this->assign('end_time', $end_time);
        $this->assign('employee_count', $employee_count);
        return view();
    }
}
