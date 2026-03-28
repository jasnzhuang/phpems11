<?php
namespace PHPEMS\user;
use function \PHPEMS\M;
/*
 * Created on 2013-5-31
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class config
{
	public $fields = array('userid','username','userpassword','usertruename','useremail','usercoin','userregip','userregtime','usergroupid','usermoduleid');
	public $pcmenus = array(
		array(
			'icon' => 'glyphicon glyphicon-user',
			'url' => 'index.php?user-app',
			'method' => 'index',
			'title' => '个人中心',
			'weight' => '90'
		),
		array(
			'icon' => 'glyphicon glyphicon-eye-open',
			'url' => 'index.php?user-app-verify',
			'method' => 'verify',
			'title' => '实名认证',
			'weight' => '80'
		),
		array(
			'icon' => 'glyphicon glyphicon-list-alt',
			'url' => 'index.php?user-app-exam',
			'method' => 'exam',
			'title' => '我的考场',
			'weight' => '70'
		),
		array(
			'icon' => 'glyphicon glyphicon-play-circle',
			'url' => 'index.php?user-app-course',
			'method' => 'course',
			'title' => '我的课程',
			'weight' => '60'
		),
		array(
			'icon' => 'glyphicon glyphicon-question-sign',
			'url' => 'index.php?user-app-ask',
			'method' => 'ask',
			'title' => '我的提问',
			'weight' => '50'
		),
		array(
			'icon' => 'glyphicon glyphicon-book',
			'url' => 'index.php?user-app-certificate',
			'method' => 'certificate',
			'title' => '我的证书',
			'weight' => '40'
		),
		array(
			'icon' => 'glyphicon glyphicon-yen',
			'url' => 'index.php?user-app-payfor',
			'method' => 'payfor',
			'title' => '订单充值',
			'weight' => '30'
		),
		array(
			'icon' => 'glyphicon glyphicon-cog',
			'url' => 'index.php?user-app-privatement',
			'method' => 'privatement',
			'title' => '个人设置',
			'weight' => '20'
		)
	);
	public $mobilemenus = array(
		array(
			'icon' => 'glyphicon glyphicon-eye-open',
			'url' => 'index.php?user-phone-verify',
			'method' => 'verify',
			'title' => '实名认证',
			'weight' => '80'
		),
		array(
			'icon' => 'glyphicon glyphicon-list-alt',
			'url' => 'index.php?user-phone-exam',
			'method' => 'exam',
			'title' => '我的考场',
			'weight' => '70'
		),
		array(
			'icon' => 'glyphicon glyphicon-play-circle',
			'url' => 'index.php?user-phone-course',
			'method' => 'course',
			'title' => '我的课程',
			'weight' => '60'
		),
		array(
			'icon' => 'glyphicon glyphicon-question-sign',
			'url' => 'index.php?user-phone-ask',
			'method' => 'ask',
			'title' => '我的提问',
			'weight' => '50'
		),
		array(
			'icon' => 'glyphicon glyphicon-book',
			'url' => 'index.php?user-phone-certificate',
			'method' => 'certificate',
			'title' => '我的证书',
			'weight' => '40'
		),
		array(
			'icon' => 'glyphicon glyphicon-yen',
			'url' => 'index.php?user-phone-payfor',
			'method' => 'payfor',
			'title' => '订单充值',
			'weight' => '30'
		),
		array(
			'icon' => 'glyphicon glyphicon-cog',
			'url' => 'index.php?user-phone-privatement-modifypass',
			'method' => 'privatement',
			'title' => '修改密码',
			'weight' => '20'
		)
	);

	public function getMenus()
	{
		if(M('ev')->isMobile())
		{
			$menus = $this->mobilemenus;
			$menus = M('plugin')->filter('userMobileMenus',$menus);
		}
		else
		{
			$menus = $this->pcmenus;
			$menus = M('plugin')->filter('userPcMenus',$menus);
		}
		usort($menus,function($a,$b){
			if($a['weight'] == $b['weight'])return 0;
			return ($a['weight'] > $b['weight']) ? -1 : 1;
		});
		return $menus;
	}
}
?>
