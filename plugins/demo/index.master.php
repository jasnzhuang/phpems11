<?php
namespace PHPEMS\plugins\demo;
use function PHPEMS\M;
use function PHPEMS\R;

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
		if($this->user['groupid'] != 1 )
		{
			$message = array(
				'statusCode' => 300,
				"message" => "请您重新登录",
				"callbackType" => 'forward',
				"forwardUrl" => "index.php?core-master-login"
			);
			R($message);
		}
	}

	public function display()
	{
		$plugin = M('plugin')->getPluginByName('demo');
		if(M('ev')->get('pluginconfig'))
		{
			$args = M('ev')->get('args');
			M('plugin')->modifyPlugin('demo',['pluginsetting' => $args]);
			$message = array(
				'statusCode' => 200,
				"message" => "操作成功",
				"callbackType" => "forward",
				"forwardUrl" => "reload"
			);
			R($message);
		}
		else
		{
			$setting = $plugin['pluginsetting'];
			$this->tpl->assign('setting',$setting);
			$this->tpl->display('index');
		}
	}
}


?>
