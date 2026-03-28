<?php

namespace PHPEMS\plugins\demo;
use function PHPEMS\M;

class index
{
    public $user;
    public $session;
	public $tpl;

    public function __construct()
    {
        $this->tpl = M('tpl')->setPluginType();
        $this->session = M('session')->getSessionUser();
        $this->user = M('user','user')->getUserById($this->session['sessionuserid']);
    }
	
	public function display()
	{
        $this->tpl->display('index');
	}
}