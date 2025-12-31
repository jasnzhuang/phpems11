<?php
namespace PHPEMS;
/*
 * Created on 2016-5-19
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class action extends app
{
	public function display()
	{
		$action = M('ev')->url(3);
		if(!method_exists($this,$action))
		$action = "index";
		$this->$action();
		exit;
	}

	public function off()
	{
		$plugin = M('ev')->get('plugin');
		M('plugin')->modifyPlugin($plugin,['pluginstatus' => 0]);
		$message = array(
			'statusCode' => 200,
			"message" => "操作成功",
			"callbackType" => "forward",
			"forwardUrl" => "reload"
		);
		R($message);
	}

	public function on()
	{
		$pluginname = M('ev')->get('plugin');
		$plugin = M('plugin')->getPluginByName($pluginname);
		if($plugin['plugin'])
		{
			M('plugin')->modifyPlugin($plugin['plugin'],['pluginstatus' => 1]);
		}
		else
		{
			$id = M('plugin')->installPlugin($pluginname);
			if(!$id)
			{
				$message = array(
					'statusCode' => 300,
					"message" => "插件开启失败"
				);
				R($message);
			}
		}
		$message = array(
			'statusCode' => 200,
			"message" => "操作成功",
			"callbackType" => "forward",
			"forwardUrl" => "reload"
		);
		R($message);
	}

	public function index()
	{
		$plugins = M('plugin')->getLocalPlugins();
		$actives = M('plugin')->getActivePlugins();
		$plugins = $plugins['dir'];
		foreach($plugins as $key => $plugin)
		{
			$config = P('config',$plugin['name']);
			$plugins[$key]['title'] = $config->title;
			$plugins[$key]['describe'] = $config->describe;
			$plugins[$key]['manageUrl'] = $config->manageUrl;
			if(in_array($plugin['name'],$actives))$plugins[$key]['actived'] = 1;
		}
		M('tpl')->assign('plugins',$plugins);
		M('tpl')->display('plugins');
	}
}


?>
