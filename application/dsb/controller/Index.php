<?php
namespace app\dsb\controller;
use think\Request;
use think\Controller;
class Index extends Controller{
    public function __construct() {
        parent::__construct();
        $company = session('company');
        $user = session('user');
        $nav = strtolower(Request::instance()->action());
        if ($company && $user) {
            $this->redirect('/dsb/home/index.html');
        }
        $this->assign('nav', $nav);
    }

    public function index() {
        return view();
    }

    public function about() {
        return view();
    }
}