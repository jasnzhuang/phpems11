<?php
namespace PHPEMS;
/*
 * Created on 2025-08-01
 *
 * 数据分析模型类
 */

class dataanalysis_dataanalysis
{
	public $G;
	private $db;
	private $pdosql;
	private $basic;
	private $exam;
	private $user;
	
	public function __construct()
	{
		
	}
	
	public function _init()
	{
		$this->pdosql = \PHPEMS\ginkgo::make('pdosql');
		$this->db = \PHPEMS\ginkgo::make('pepdo');
		$this->basic = \PHPEMS\ginkgo::make('basic','exam');
		$this->exam = \PHPEMS\ginkgo::make('exam','exam');
		$this->user = \PHPEMS\ginkgo::make('user','user');
	}
	
	/**
	 * 获取用户组列表
	 */
	public function getUserGroups()
	{
		$sql = "SELECT * FROM x2_user_group ORDER BY groupid ASC";
		return $this->db->fetchAll($sql);
	}
	
	/**
	 * 获取考场列表
	 */
	public function getExamRooms()
	{
		$sql = "SELECT * FROM x2_exams WHERE examtype = 1 ORDER BY examid DESC";
		return $this->db->fetchAll($sql);
	}
	
	/**
	 * 获取考试分析数据
	 * @param array $params 筛选参数
	 * @return array 分析结果
	 */
	public function getExamAnalysis($params = array())
	{
		$groupId = isset($params['group_id']) ? intval($params['group_id']) : 0;
		$examId = isset($params['exam_id']) ? intval($params['exam_id']) : 0;
		$startTime = isset($params['start_time']) ? trim($params['start_time']) : '';
		$endTime = isset($params['end_time']) ? trim($params['end_time']) : '';
		
		// 基础查询 - 使用 x2_examhistory 表替代 x2_examscore
		$sql = "SELECT 
				e.examid, 
				e.exam, 
				e.examsubject, 
				e.examsetting,
				COUNT(DISTINCT eh.ehuserid) as total_users,
				COUNT(eh.ehid) as total_attempts,
				AVG(eh.ehscore) as avg_score,
				MAX(eh.ehscore) as max_score,
				MIN(eh.ehscore) as min_score,
				SUM(CASE WHEN eh.ehscore >= 60 THEN 1 ELSE 0 END) as pass_count,
				SUM(CASE WHEN eh.ehscore >= 90 THEN 1 ELSE 0 END) as excellent_count
				FROM x2_exams e
				LEFT JOIN x2_examhistory eh ON e.examid = eh.ehexamid
				WHERE 1=1";
		
		$where = array();
		
		if ($groupId > 0) {
			$sql .= " AND eh.ehuserid IN (SELECT userid FROM x2_user WHERE usergroupid = :group_id)";
			$where[':group_id'] = $groupId;
		}
		
		if ($examId > 0) {
			$sql .= " AND e.examid = :exam_id";
			$where[':exam_id'] = $examId;
		}
		
		if (!empty($startTime)) {
			$sql .= " AND eh.ehstarttime >= :start_time";
			$where[':start_time'] = strtotime($startTime);
		}
		
		if (!empty($endTime)) {
			$sql .= " AND eh.ehstarttime <= :end_time";
			$where[':end_time'] = strtotime($endTime . ' 23:59:59');
		}
		
		$sql .= " GROUP BY e.examid ORDER BY e.examid DESC";
		
		$examData = $this->db->fetchAll($sql, $where);
		
		// 处理数据
		foreach ($examData as &$exam) {
			$exam['pass_rate'] = $exam['total_users'] > 0 ? round(($exam['pass_count'] / $exam['total_users']) * 100, 2) : 0;
			$exam['excellent_rate'] = $exam['total_users'] > 0 ? round(($exam['excellent_count'] / $exam['total_users']) * 100, 2) : 0;
			$exam['avg_score'] = round($exam['avg_score'], 2);
			
			// 获取分数段分布
			$exam['score_distribution'] = $this->getScoreDistribution($exam['examid'], $params);
			
			// 获取题目正确率
			$exam['question_accuracy'] = $this->getQuestionAccuracy($exam['examid'], $params);
		}
		
		return $examData;
	}
	
	/**
	 * 获取分数段分布
	 */
	private function getScoreDistribution($examId, $params)
	{
		$sql = "SELECT 
				COUNT(CASE WHEN ehscore < 60 THEN 1 END) as fail_count,
				COUNT(CASE WHEN ehscore >= 60 AND ehscore < 70 THEN 1 END) as pass_count,
				COUNT(CASE WHEN ehscore >= 70 AND ehscore < 80 THEN 1 END) as good_count,
				COUNT(CASE WHEN ehscore >= 80 AND ehscore < 90 THEN 1 END) as very_good_count,
				COUNT(CASE WHEN ehscore >= 90 THEN 1 END) as excellent_count
				FROM x2_examhistory 
				WHERE ehexamid = :exam_id";
		
		$where = array(':exam_id' => $examId);
		
		if (isset($params['group_id']) && $params['group_id'] > 0) {
			$sql .= " AND ehuserid IN (SELECT userid FROM x2_user WHERE usergroupid = :group_id)";
			$where[':group_id'] = $params['group_id'];
		}
		
		if (!empty($params['start_time'])) {
			$sql .= " AND ehstarttime >= :start_time";
			$where[':start_time'] = strtotime($params['start_time']);
		}
		
		if (!empty($params['end_time'])) {
			$sql .= " AND ehstarttime <= :end_time";
			$where[':end_time'] = strtotime($params['end_time'] . ' 23:59:59');
		}
		
		return $this->db->fetch($sql, $where);
	}
	
	/**
	 * 获取题目正确率
	 */
	private function getQuestionAccuracy($examId, $params)
	{
		$sql = "SELECT 
				q.questionid,
				q.question,
				q.questiontype,
				COUNT(eq.eqid) as total_attempts,
				SUM(CASE WHEN eq.eqisright = 1 THEN 1 ELSE 0 END) as correct_count,
				ROUND(SUM(CASE WHEN eq.eqisright = 1 THEN 1 ELSE 0 END) * 100.0 / COUNT(eq.eqid), 2) as accuracy_rate
				FROM x2_exam_questions eq
				LEFT JOIN x2_questions q ON eq.eqquestionid = q.questionid
				WHERE eq.eqexamid = :exam_id
				GROUP BY q.questionid, q.question, q.questiontype
				ORDER BY accuracy_rate ASC";
		
		$where = array(':exam_id' => $examId);
		
		return $this->db->fetchAll($sql, $where);
	}
	
	/**
	 * 获取考试趋势数据
	 */
	public function getExamTrends($params = array())
	{
		$sql = "SELECT 
				FROM_UNIXTIME(ehstarttime, '%Y-%m-%d') as exam_date,
				COUNT(DISTINCT ehuserid) as daily_users,
				AVG(ehscore) as daily_avg_score,
				COUNT(ehid) as daily_attempts
				FROM x2_examhistory 
				WHERE 1=1";
		
		$where = array();
		
		if (isset($params['group_id']) && $params['group_id'] > 0) {
			$sql .= " AND ehuserid IN (SELECT userid FROM x2_user WHERE usergroupid = :group_id)";
			$where[':group_id'] = $params['group_id'];
		}
		
		if (isset($params['exam_id']) && $params['exam_id'] > 0) {
			$sql .= " AND ehexamid = :exam_id";
			$where[':exam_id'] = $params['exam_id'];
		}
		
		if (!empty($params['start_time'])) {
			$sql .= " AND ehstarttime >= :start_time";
			$where[':start_time'] = strtotime($params['start_time']);
		}
		
		if (!empty($params['end_time'])) {
			$sql .= " AND ehstarttime <= :end_time";
			$where[':end_time'] = strtotime($params['end_time'] . ' 23:59:59');
		}
		
		$sql .= " GROUP BY FROM_UNIXTIME(ehstarttime, '%Y-%m-%d') ORDER BY exam_date ASC";
		
		return $this->db->fetchAll($sql, $where);
	}
	
	/**
	 * 获取用户组对比数据
	 */
	public function getGroupComparison($params = array())
	{
		$sql = "SELECT 
				ug.groupid,
				ug.groupname,
				COUNT(DISTINCT eh.ehuserid) as total_users,
				COUNT(eh.ehid) as total_attempts,
				AVG(eh.ehscore) as avg_score,
				SUM(CASE WHEN eh.ehscore >= 60 THEN 1 ELSE 0 END) as pass_count,
				ROUND(SUM(CASE WHEN eh.ehscore >= 60 THEN 1 ELSE 0 END) * 100.0 / COUNT(DISTINCT eh.ehuserid), 2) as pass_rate
				FROM x2_user_group ug
				LEFT JOIN x2_user u ON ug.groupid = u.usergroupid
				LEFT JOIN x2_examhistory eh ON u.userid = eh.ehuserid
				WHERE 1=1";
		
		$where = array();
		
		if (isset($params['exam_id']) && $params['exam_id'] > 0) {
			$sql .= " AND eh.ehexamid = :exam_id";
			$where[':exam_id'] = $params['exam_id'];
		}
		
		if (!empty($params['start_time'])) {
			$sql .= " AND eh.ehstarttime >= :start_time";
			$where[':start_time'] = strtotime($params['start_time']);
		}
		
		if (!empty($params['end_time'])) {
			$sql .= " AND eh.ehstarttime <= :end_time";
			$where[':end_time'] = strtotime($params['end_time'] . ' 23:59:59');
		}
		
		$sql .= " GROUP BY ug.groupid, ug.groupname ORDER BY avg_score DESC";
		
		return $this->db->fetchAll($sql, $where);
	}
}
?>