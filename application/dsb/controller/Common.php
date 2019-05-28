<?php
namespace app\dsb\controller;
use think\Request;
use think\Controller;
class Common extends Controller {
	public function __construct() {
		parent::__construct();
		$nav = strtolower(Request::instance()->controller());
		$this->company = session('company');
        $this->user = session('user');
		if ( !$this->company || !$this->user) {
			$this->redirect('/dsb/index/index.html');
		}
		if ($this->company->status == 1) {
			$this->redirect('/dsb/user/step3.html');
		}
		$this->assign('nav', $nav);
		$this->assign('company', $this->company);
		$this->assign('user', $this->user);
		$this->assign('error_msg', '');
	}
}
