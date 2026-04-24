<?php
namespace PHPEMS;
/*
 * Created on 2025-08-01
 *
 * 数据分析控制器
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

	private function index()
	{
		// 引入数据分析模型类
		require_once PEPATH . '/app/dataanalysis/cls/dataanalysis.cls.php';
		$dataanalysis = new \PHPEMS\dataanalysis_dataanalysis();
		$dataanalysis->_init();
		
		// 调试模式检查
		$debug = M('ev')->get('debug');
		if ($debug) {
			M('tpl')->assign('usergroups',$dataanalysis->getUserGroups());
			M('tpl')->assign('examrooms',$dataanalysis->getExamRooms());
			M('tpl')->display('debug_page');
			return;
		}
		
		// 尝试获取数据，如果失败则使用空数组
		try {
			$usergroups = $dataanalysis->getUserGroups();
		} catch (Exception $e) {
			$usergroups = array();
			error_log('获取用户组数据失败: ' . $e->getMessage());
		}
		
		try {
			$examrooms = $dataanalysis->getExamRooms();
		} catch (Exception $e) {
			$examrooms = array();
			error_log('获取考场数据失败: ' . $e->getMessage());
		}
		
		M('tpl')->assign('usergroups', $usergroups);
		M('tpl')->assign('examrooms', $examrooms);
		M('tpl')->display('dataanalysis_index_robust');
	}
	
	private function analysis()
	{
		// 引入数据分析模型类
		require_once PEPATH . '/app/dataanalysis/cls/dataanalysis.cls.php';
		$dataanalysis = new \PHPEMS\dataanalysis_dataanalysis();
		$dataanalysis->_init();
		
		$groupId = M('ev')->get('group_id');
		$examId = M('ev')->get('exam_id');
		$startTime = M('ev')->get('start_time');
		$endTime = M('ev')->get('end_time');
		
		$params = array(
			'group_id' => $groupId,
			'exam_id' => $examId,
			'start_time' => $startTime,
			'end_time' => $endTime
		);
		
		$examData = $dataanalysis->getExamAnalysis($params);
		$trendData = $dataanalysis->getExamTrends($params);
		$groupData = $dataanalysis->getGroupComparison($params);
		
		M('tpl')->assign('params', $params);
		M('tpl')->assign('examData', $examData);
		M('tpl')->assign('trendData', $trendData);
		M('tpl')->assign('groupData', $groupData);
		M('tpl')->assign('usergroups', $dataanalysis->getUserGroups());
		M('tpl')->assign('examrooms', $dataanalysis->getExamRooms());
		M('tpl')->display('dataanalysis_analysis');
	}
	
	private function export()
	{
		// 引入数据分析模型类
		require_once PEPATH . '/app/dataanalysis/cls/dataanalysis.cls.php';
		$dataanalysis = new \PHPEMS\dataanalysis_dataanalysis();
		$dataanalysis->_init();
		
		$groupId = M('ev')->get('group_id');
		$examId = M('ev')->get('exam_id');
		$startTime = M('ev')->get('start_time');
		$endTime = M('ev')->get('end_time');
		
		$params = array(
			'group_id' => $groupId,
			'exam_id' => $examId,
			'start_time' => $startTime,
			'end_time' => $endTime
		);
		
		$examData = $dataanalysis->getExamAnalysis($params);
		
		// 导出Excel
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="exam_analysis_' . date('YmdHis') . '.xls"');
		header('Cache-Control: max-age=0');
		
		echo "考试ID\t考试名称\t科目\t参考人数\t平均分\t最高分\t最低分\t及格率\t优秀率\n";
		
		foreach ($examData as $exam) {
			echo "{$exam['examid']}\t{$exam['exam']}\t{$exam['examsubject']}\t{$exam['total_users']}\t{$exam['avg_score']}\t{$exam['max_score']}\t{$exam['min_score']}\t{$exam['pass_rate']}%\t{$exam['excellent_rate']}%\n";
		}
		
		exit;
	}
}
?>