<?php
namespace PHPEMS\exam;
use function \PHPEMS\M;
/*
 * Created on 2013-5-31
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
class config
{
	public $selectorder = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
	public $ols = array(1 => '一','二','三','四','五','六','七','八','九','十','十一','十二','十三','十四','十五','十六','十七','十八','十九','二十');
	public $sectionorder = array(1=>'第一章','第二章','第三章','第四章','第五章','第六章','第七章','第八章','第九章','第十章','第十一章','第十二章','第十三章','第十四章','第十五章','第十六章','第十七章','第十八章','第十九章','第二十章','第二十一章','第二十二章');
	public $pcmenus = array(
		array(
			'icon' => 'glyphicon glyphicon-pencil',
			'url' => 'index.php?exam-app-lesson',
			'method' => 'lesson',
			'title' => '章节练习',
			'weight' => '90',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'glyphicon glyphicon-bell',
			'url' => 'index.php?exam-app-recite',
			'method' => 'recite',
			'title' => '背题模式',
			'weight' => '80',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'glyphicon glyphicon-refresh',
			'url' => 'index.php?exam-app-exercise',
			'method' => 'exercise',
			'title' => '强化训练',
			'weight' => '70',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'glyphicon glyphicon-time',
			'url' => 'index.php?exam-app-exampaper',
			'method' => 'exampaper',
			'title' => '模拟考试',
			'weight' => '60',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'glyphicon glyphicon-list-alt',
			'url' => 'index.php?exam-app-history',
			'method' => 'history',
			'title' => '考试记录',
			'weight' => '50',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'glyphicon glyphicon-star',
			'url' => 'index.php?exam-app-favor',
			'method' => 'favor',
			'title' => '习题收藏',
			'weight' => '40',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'glyphicon glyphicon-asterisk',
			'url' => 'index.php?exam-app-record',
			'method' => 'record',
			'title' => '错题记录',
			'weight' => '30',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'glyphicon glyphicon-stats',
			'url' => 'index.php?exam-app-score',
			'method' => 'score',
			'title' => '成绩单',
			'weight' => '20',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'glyphicon glyphicon-equalizer',
			'url' => 'index.php?exam-app-questions',
			'method' => 'questions',
			'title' => '试题库',
			'weight' => '10',
			'basictype' => array(0)
		)
	);
	public $mobilemenus = array(
		array(
			'icon' => 'fa-solid fa-marker',
			'url' => 'index.php?exam-phone-lesson',
			'method' => 'lesson',
			'title' => '章节练习',
			'weight' => '90',
			'intro' => '逐个章节、知识点刷题练习',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'fa-solid fa-bell',
			'url' => 'index.php?exam-phone-recite',
			'method' => 'recite',
			'title' => '背题模式',
			'weight' => '80',
			'intro' => '逐个章节、知识点逐题背诵',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'fa-solid fa-pen-ruler',
			'url' => 'index.php?exam-phone-exercise',
			'method' => 'exercise',
			'title' => '强化训练',
			'weight' => '70',
			'intro' => '选择考试范围、题型、数量组卷练习',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'fa-solid fa-laptop-file',
			'url' => 'index.php?exam-phone-exampaper',
			'method' => 'exampaper',
			'title' => '模拟考试',
			'weight' => '60',
			'intro' => '仿真模拟考试，体验真实考试环境',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'fa-solid fa-record-vinyl',
			'url' => 'index.php?exam-phone-history',
			'method' => 'history',
			'title' => '考试记录',
			'weight' => '50',
			'intro' => '强化训练、模拟和正式考试记录',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'fa-solid fa-flag',
			'url' => 'index.php?exam-phone-favor',
			'method' => 'favor',
			'title' => '习题收藏',
			'weight' => '40',
			'intro' => '试题收藏 便捷背题',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'fa-solid fa-bug',
			'url' => 'index.php?exam-phone-record',
			'method' => 'record',
			'title' => '错题记录',
			'weight' => '30',
			'intro' => '错题练习 随机检测',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'fa-solid fa-chart-line',
			'url' => 'index.php?exam-phone-score',
			'method' => 'score',
			'title' => '成绩单',
			'weight' => '20',
			'intro' => '本考场所有学员分数及排名',
			'basictype' => array(0,1)
		),
		array(
			'icon' => 'fa-solid fa-map-location',
			'url' => 'index.php?exam-phone-questions',
			'method' => 'questions',
			'title' => '试题库',
			'weight' => '10',
			'intro' => '全量试题搜索',
			'basictype' => array(0)
		)
	);

	public function getMenus()
	{
		if(M('ev')->isMobile())
		{
			$menus = $this->mobilemenus;
			$menus = M('plugin')->filter('examMobileMenus',$menus);
		}
		else
		{
			$menus = $this->pcmenus;
			$menus = M('plugin')->filter('examPcMenus',$menus);
		}
		usort($menus,function($a,$b){
			if($a['weight'] == $b['weight'])return 0;
			return ($a['weight'] > $b['weight']) ? -1 : 1;
		});
		return $menus;
	}
}
?>
