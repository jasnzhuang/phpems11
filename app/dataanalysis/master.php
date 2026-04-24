<?php
namespace PHPEMS;
/*
 * Created on 2025-08-01
 *
 * 数据分析模块主文件
 */

class app
{
	public $user;
	public $session;

	public function __construct()
	{
		$this->session = M('session')->getSessionUser();
		$this->user = M('user','user')->getUserById($this->session['sessionuserid']);
		if($this->user['groupid'] != 1)
		{
			$message = array(
				'statusCode' => 300,
				"message" => "请您重新登录",
				"callbackType" => 'forward',
				"forwardUrl" => "index.php?core-master-login"
			);
			R($message);
		}
		$localapps = M('apps','core')->getLocalAppList();
		$apps = M('apps','core')->getAppList();
		M('tpl')->assign('localapps',$localapps);
		M('tpl')->assign('apps',$apps);
		M('tpl')->assign('_user',$this->user);
		M('tpl')->assign('action',M('ev')->url(2)?M('ev')->url(2):'user');
	}
}
?>