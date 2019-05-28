<?php
namespace app\dsb\controller;
use think\Request;
use PHPExcel_IOFactory;
use PHPExcel_Cell;
use app\dsb\model\Employee as EmployeeModel;
use app\dsb\model\Detail as DetailModel;
use app\dsb\model\History as HistoryModel;
use app\dsb\model\Salary as SalaryModel;
class Salary extends Common
{
    private $bitmap = [false, false, false, false, false, false, false, false, false, false, false];
    private $salary = 0.00;
    private $tax = 0.00;
    private $salary_list = [];
    private $type = [0=>'不发送', 1=>'短信', 2=>'邮件', 3=>'短信+邮件'];
    public function __construct() {
        parent::__construct();
    }

    public function index() 
    {
        if (Request::instance()->isPost()) {
            $month = input('month');
            $batch = input('batch');
            $upload = Request::instance()->file('excel');
            $xls = explode('.', $upload->getInfo('name'))[1];
            $reader = PHPExcel_IOFactory::createReader($xls == 'xls' ? 'Excel5' : 'Excel2007');
            $excel = $reader->load($upload->getInfo('tmp_name'));
            $sheetCount = $excel->getSheetCount();
            $sheet = $excel->getSheet(0);
            $row = $sheet->getHighestRow();
            $column = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());  
            if ($sheetCount > 1) {
                $this->assign('error_msg', '上传表格仅支持含一个sheet工资表');
                return view();
            }
            if ($row > 10000) {
                $this->assign('error_msg', '请新建工作簿，将此表格复制选择性粘贴为数值后上传');
                return view();
            }
            for ($r = 1; $r <= $row; $r++) {
                for ($c = 0; $c < $column; $c++) {
                    $value = $sheet->getCellByColumnAndRow($c, $r)->getValue();
                    if (strpos($value, '序号') !== false) {
                        if ( !$this->bitmap[0]) $this->bitmap[0] = $r;
                        $this->bitmap[4] = $c;
                        $this->salary_list[0][] = ['id'=>$c, 'title'=>'序号', 'field'=>'c'.$c, 'rowspan'=>1, 'colspan'=>1, 'align'=>'center'];
                    }else if (strpos($value, '姓名') !== false) {
                        if ( !$this->bitmap[0]) $this->bitmap[0] = $r;
                        $this->bitmap[5] = $c;
                        $this->salary_list[0][] = ['id'=>$c, 'title'=>'姓名', 'field'=>'c'.$c, 'rowspan'=>1, 'colspan'=>1, 'align'=>'center'];
                    }else if (strpos($value, '身份证') !== false || strpos($value, '证件') !== false) {
                        if ( !$this->bitmap[0]) $this->bitmap[0] = $r;
                        $this->bitmap[6] = $c;
                        $this->salary_list[0][] = ['id'=>$c, 'title'=>$value, 'field'=>'c'.$c, 'rowspan'=>1, 'colspan'=>1, 'align'=>'center'];
                    }else if (strpos($value, '手机号') !== false) {
                        $this->bitmap[7] = $c;
                    }else if (strpos($value, '邮箱') !== false) {
                        $this->bitmap[8] = $c;
                    }else if ($r == $this->bitmap[0] && strpos($value, '实发') !== false) {
                        $this->bitmap[9] = $c;
                    }else if ($r == $this->bitmap[0] && strpos($value, '个税') !== false) {
                        $this->bitmap[10] = $c;
                    }
                }
                if ($this->bitmap[5] !== false && $this->bitmap[6] !== false && $this->bitmap[9] !== false) {
                    $real_name = $sheet->getCellByColumnAndRow($this->bitmap[5], $r)->getValue();
                    $idcard = $sheet->getCellByColumnAndRow($this->bitmap[6], $r)->getValue();
                    $salary = $sheet->getCellByColumnAndRow($this->bitmap[9], $r)->getValue();
                    if ($real_name && !preg_match('/[\x{4e00}-\x{9fa5}]/u', $idcard) && $salary) {
                        if ( !$this->bitmap[2]) $this->bitmap[2] = $r;
                        $this->bitmap[3] = $r;
                    }
                }
            };
            $is_batch = SalaryModel::where(['company_id'=>$this->company['id'], 'month'=>$month, 'batch'=>$batch, 'status'=>1])->count();
            if ( !$month) {
                $this->assign('error_msg', '月份为必传项');
                return view();
            }
            if ( !$batch) {
                $this->assign('error_msg', '批次为必传项');
                return view();
            }
            if ($is_batch) {
                $this->assign('error_msg', '当前批次已发放');
                return view();
            }
            if ($this->bitmap[5] === false || $this->bitmap[6] === false || $this->bitmap[9] === false) {
                $this->assign('error_msg', '表格上未含“姓名、身份证、实发工资”信息');
                return view();
            }
            $pos = $index = $limit = 0;
            for ($c = 0; $c < $column; $c++) {
                $value = $sheet->getCellByColumnAndRow($c, $this->bitmap[0])->getValue();
                if ( !$pos && !$value) $pos = $c;
            }
            for ($r = $this->bitmap[0] + 1; $r < $this->bitmap[2]; $r++) {
                $value = $sheet->getCellByColumnAndRow($pos, $r)->getValue();
                if ($value) $this->bitmap[1] = $r;
            }
            if ($this->bitmap[1]) {
                for ($c = 0; $c < $column; $c++) {
                    $first_table = $sheet->getCellByColumnAndRow($c, $this->bitmap[0])->getValue();
                    $second_table = $sheet->getCellByColumnAndRow($c, $this->bitmap[1])->getValue();
                    if ($first_table || $second_table) {
                        if ( !in_array($c, [$this->bitmap[4], $this->bitmap[5], $this->bitmap[6]])) {
                            if ($first_table) {
                                $index = $c;
                                $this->salary_list[1][$c] = ['id'=>$c, 'title'=>$first_table, 'field'=>'c'.$c, 'rowspan'=>2, 'colspan'=>1, 'align'=>'center'];
                            }else{
                                $this->salary_list[1][$index]['rowspan'] = 1;
                                $this->salary_list[1][$index]['colspan']++;
                                unset($this->salary_list[1][$index]['field']);
                            }
                            if ($second_table) {
                                $this->salary_list[2][] = ['id'=>$c, 'title'=>$second_table, 'field'=>'c'.$c, 'rowspan'=>1, 'colspan'=>1, 'align'=>'center'];
                            }
                        }
                        $limit = $c + 1;
                    }
                }
            }else{
                $this->salary_list[1] = [];
                for ($c = 1; $c < $column; $c++) {
                    $value = $sheet->getCellByColumnAndRow($c, $this->bitmap[0])->getValue();
                    if ($value) {
                        if ( !in_array($c, [$this->bitmap[4], $this->bitmap[5], $this->bitmap[6]])) {
                            $this->salary_list[2][] = ['id'=>$c, 'title'=>$value, 'field'=>'c'.$c, 'rowspan'=>1, 'colspan'=>1, 'align'=>'center'];
                        }
                        $limit = $c + 1;
                    }
                }
            }
            $idcard_list = $error_list = $success_list = [];
            for ($r = $this->bitmap[2]; $r <= $this->bitmap[3]; $r++) {
                $list['c'.$limit] = '';
                for ($c = 0; $c < $limit; $c++) {
                    $cell = $sheet->getCellByColumnAndRow($c, $r);
                    try{
                        $value = $cell->getCalculatedValue(); 
                    }catch(\Exception $e) {
                        $value = $cell->getValue();
                    }
                    $format = $cell->getStyle($cell->getCoordinate())->getNumberFormat();
                    $formatcode = $format->getFormatCode();
                    if (preg_match('/^(\[\$[A-Z]*-[0-9A-F]*\])*[hmsdy]/i', $formatcode) && !is_idcard($value)) {
                        $value = $cell->getFormattedValue();
                    }
                    if ($c == $this->bitmap[5] && !$value) $list['c'.$limit] .= '<span class="s-red">姓名不能为空</span></br>';
                    if ($c == $this->bitmap[6]) {
                        if ( !is_idcard($value) && !is_passport($value)) $list['c'.$limit] .= '<span class="s-red">证件号码格式错误</span></br>';
                        if (in_array($value, $idcard_list)) $list['c'.$limit] .= '<span class="s-red">身份证重复</span></br>';
                        $idcard_list[] = $value;
                    }
                    if ($this->bitmap[7] !== false && $c == $this->bitmap[7] && $value && !is_mobile($value)) $list['c'.$limit] .= '<span class="s-red">手机号格式错误</span></br>';
                    if ($this->bitmap[8] !== false && $c == $this->bitmap[8] && $value && !is_email($value)) $list['c'.$limit] .= '<span class="s-red">邮箱格式错误</span></br>';
                    if ($c == $this->bitmap[9]) $this->salary = bcadd($this->salary, $value, 3);
                    if ($this->bitmap[10] !== false && $c == $this->bitmap[10]) $this->tax = bcadd($this->tax, $value, 3);
                    if ( !$value) {
                        $value = 0;
                    }else if (strpos($value, '.') !== false && !is_email($value)) {
                        $value = bcadd(round($value, 2), 0, 2);
                    }
                    $list['c'.$c] = $value;
                }
                if ($list['c'.$limit]) { 
                    $error_list[] = $list; 
                }else{ 
                    unset($list['c'.$limit]); 
                    $success_list[] = $list; 
                }
            };
            if ($error_list) {
                $this->salary_list[0][] = ['id'=>$c, 'title'=>'异常原因', 'field'=>'c'.$limit, 'rowspan'=>1, 'colspan'=>1, 'align'=>'center'];
            }
            $this->salary_list[1] = array_values($this->salary_list[1]);
            $this->salary_list[3] = array_merge($error_list, $success_list);
            session('month', $month);
            session('batch', $batch);
            session('bitmap', $this->bitmap);
            session('salary', round($this->salary, 2));
            session('tax', round($this->tax, 2));
            session('error_list', $error_list);
            session('salary_list', $this->salary_list); 
            $this->redirect('/dsb/salary/step2.html');
        }
        return view();
    }

    public function step2() 
    {
        $month = session('month');
        $batch = session('batch');
        $bitmap = session('bitmap');
        $salary = session('salary');
        $tax = session('tax');
        $error_list = session('error_list');
        $salary_list = session('salary_list');
        if (Request::instance()->isPost()) {
            $is_batch = SalaryModel::where(['company_id'=>$this->company['id'], 'month'=>$month, 'batch'=>$batch, 'status'=>1])->count();
            if ($is_batch) {
                $this->assign('error_msg', '当前批次已发放');
            }else if ($error_list) {
                $this->assign('error_msg', '上传数据异常');
            }else{ 
                $this->redirect('/dsb/salary/step3.html');
            }
        }
        $this->assign('month', $month);
        $this->assign('batch', $batch);
        $this->assign('bitmap', $bitmap);
        $this->assign('salary', $salary);
        $this->assign('tax', $tax);
        $this->assign('error_list', $error_list);
        $this->assign('salary_list', $salary_list);
        return view();
    }

    public function step3() 
    {
        $month = session('month');
        $batch = session('batch');
        $bitmap = session('bitmap');
        $salary = session('salary');
        $tax = session('tax');
        $error_list = session('error_list');
        $salary_list = session('salary_list');
        if (Request::instance()->isPost()) {
            $type = input('type', 0);
            $content = input('content');
            $is_batch = SalaryModel::where(['company_id'=>$this->company['id'], 'month'=>$month, 'batch'=>$batch, 'status'=>1])->count();
            if ($is_batch) {
                $this->assign('error_msg', '当前批次已发放');
            }else{
                $url = sinaShortenUrl(APP_HOST."/dsb/account/index/month/$month/batch/$batch.html"); 
                HistoryModel::create([
                    'created_at'=>time(), 'updated_at'=>time(), 'company_id'=>$this->company['id'], 'month'=>$month, 'batch'=>$batch, 
                    'num'=>count($salary_list[3]), 'salary'=>$salary, 'tax'=>$tax, 'mobile'=>$this->user['mobile'], 'type'=>$type, 'content'=>$content, 'status'=>1
                ]);
                $did_list = $cid_list = [];
                $table_list = array_merge($salary_list[0], $salary_list[1], $salary_list[2]);
                foreach ($table_list as $table) {
                    $detail = DetailModel::where(['detail_name'=>$table['title']])->find();
                    if ( !$detail) $detail = DetailModel::create(['created_at'=>time(), 'updated_at'=>time(), 'detail_name'=>$table['title']]);
                    $did_list['c'.$table['id']] = $detail['id'];
                    if ($table['colspan'] != 1) {
                        for ($c = $table['id']; $c < $table['id'] + $table['colspan']; $c++) {
                            $did_list['c'.$c] = $cid_list['c'.$c] = $detail['id'];
                        }
                    }
                }
                foreach ($salary_list[3] as $list) {
                    $employee = EmployeeModel::where(['company_id'=>$this->company['id'], 'idcard'=>$list['c'.$bitmap[6]]])->find();
                    if ($employee) {
                        if ($bitmap[7] && $list['c'.$bitmap[7]] != $employee['mobile']) {
                             EmployeeModel::where(['id'=>$employee['id']])->update(['updated_at'=>time(), 'mobile'=>$list['c'.$bitmap[7]]]);
                        }
                        if ($bitmap[8] && $list['c'.$bitmap[8]] != $employee['email']) {
                             EmployeeModel::where(['id'=>$employee['id']])->update(['updated_at'=>time(), 'email'=>$list['c'.$bitmap[8]]]);
                        }
                    }else{
                        $employee = EmployeeModel::create([
                            'created_at'=>time(), 'updated_at'=>time(), 'company_id'=>$this->company['id'], 'real_name'=>$list['c'.$bitmap[5]], 
                            'idcard'=>$list['c'.$bitmap[6]], 'mobile'=>$bitmap[7] ? $list['c'.$bitmap[7]] : '', 'email'=>$bitmap[8] ? $list['c'.$bitmap[8]] : '', 'status'=>1
                        ]);
                    }
                    foreach ($list as $k => $salary) {
                        if ($k != 'c'.$bitmap[4]) {
                            SalaryModel::create([
                                'created_at'=>time(), 'updated_at'=>time(), 'company_id'=>$this->company['id'], 'employee_id'=>$employee['id'], 
                                'month'=>$month, 'batch'=>$batch, 'did'=>$did_list[$k], 'cid'=>isset($cid_list[$k]) ? $cid_list[$k] : 0, 'value'=>$salary, 'sort'=>substr($k, 1), 'status'=>1
                            ]);
                        }
                    }
                    switch ($type) {
                        case 1:
                            if ($bitmap[7]) send_sms($list['c'.$bitmap[7]], $content.' '.$url.' 工资条属于敏感信息，请注意保密。');
                            break;
                        case 2:
                            if ($bitmap[8]) sendEmail($list['c'.$bitmap[8]], substr($month,0,4).'年'.substr($month,4).'月的工资信息', "$content <a href='{$url}'>$url</a> 工资条属于敏感信息，请注意保密。");
                            break;
                        case 3:
                            if ($bitmap[7]) send_sms($list['c'.$bitmap[7]], $content.' '.$url.' 工资条属于敏感信息，请注意保密。');
                            if ($bitmap[8]) sendEmail($list['c'.$bitmap[8]], substr($month,0,4).'年'.substr($month,4).'月的工资信息', "$content <a href='{$url}'>$url</a> 工资条属于敏感信息，请注意保密。");
                        break;
                    }    
                }       
            }
            $this->redirect('/dsb/salary/history.html');
        }
        $this->assign('month', $month);
        $this->assign('batch', $batch);
        $this->assign('salary', $salary);
        $this->assign('tax', $tax);
        $this->assign('error_list', $error_list);
        $this->assign('salary_list', $salary_list);
        return view();
    }

    public function history() {
        $month_list = [];
        $where = ['company_id'=>$this->company['id'],' status'=>1];
        $history_list = HistoryModel::field(['month'])->where($where)->order('month desc,batch desc')->select();
        foreach ($history_list as $history) {
            if ( !in_array($history['month'], $month_list)) {
                $month_list[] = $history['month'];
            }
        }
        if ($month_list) {
            $month = input('month');
            if ($month) $where['month'] = $month;
            $history_list = HistoryModel::where($where)->order('month desc,batch desc')->paginate(20);
            $this->assign('month', $month);
            $this->assign('month_list', $month_list);
            $this->assign('history_list', $history_list); 
            return view();
        }else{
            return view('empty');
        }
    }

    public function detail()
    {
        $month = input('month');
        $batch = input('batch');
        $where = ['company_id'=>$this->company['id'], 'month'=>$month, 'batch'=>$batch, 'status'=>1];
        $history = HistoryModel::where($where)->find();
        if ($history) {
            $detail_name = [];
            $detail_list = DetailModel::field('id,detail_name')->select();
            foreach ($detail_list as $detail) {
                $detail_name[$detail['id']] = $detail['detail_name'];
            }
            $salary = SalaryModel::field('employee_id')->where($where)->find();
            $is_multi = SalaryModel::where(array_merge($where, ['cid'=>['neq',0]]))->count();
            $salary_list = SalaryModel::field('did,cid')->where(array_merge($where, ['employee_id'=>$salary['employee_id']]))->order('sort asc')->select();
            foreach ($salary_list as $k => $salary) {
                if ($k < 2 || strpos($detail_name[$salary['did']], '身份证') !== false) {
                    $this->salary_list[0][] = ['title'=>$detail_name[$salary['did']], 'field'=>'c'.$salary['did'], 'align'=>'center'];
                }else{
                    if ($is_multi) {
                        if ($salary['cid']) {
                            if (isset($this->salary_list[1][$salary['cid']])) {
                                $this->salary_list[1][$salary['cid']]['colspan']++;
                            }else{
                                $this->salary_list[1][$salary['cid']] = ['title'=>$detail_name[$salary['cid']], 'rowspan'=>1, 'colspan'=>1, 'align'=>'center'];
                            }
                            $this->salary_list[2][] = ['title'=>$detail_name[$salary['did']], 'field'=>'c'.$salary['did'], 'align'=>'center'];
                        }else{
                            $this->salary_list[1][$salary['did']] = ['title'=>$detail_name[$salary['did']], 'field'=>'c'.$salary['did'], 'rowspan'=>2, 'colspan'=>1, 'align'=>'center'];
                        }
                    }else{
                        $this->salary_list[1] = [];
                        $this->salary_list[2][] = ['title'=>$detail_name[$salary['did']], 'field'=>'c'.$salary['did'], 'align'=>'center'];
                    }
                }
            }
            $salary_list = SalaryModel::field('employee_id,did,value')->where($where)->order('employee_id asc,sort asc')->select();
            foreach ($salary_list as $salary) {
                $this->salary_list[3][$salary['employee_id']]['c'.$salary['did']] = $salary['value'];
            }
            $this->salary_list[1] = array_values($this->salary_list[1]);
            $this->salary_list[3] = array_values($this->salary_list[3]);
        }else{
            $this->redirect('/dsb/salary/history.html');
        }
        $this->assign('history', $history);
        $this->assign('salary_list', $this->salary_list);
        return view();
    }
}
