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
		$action = M("ev")->url(3);
		if(!method_exists($this,$action))
		$action = "index";
		$search = M("ev")->get('search');
		$this->search = $search;
		$this->u = '';
		if($search)
		{
			M("tpl")->assign('search',$search);
			foreach($search as $key => $arg)
			{
				$this->u .= "&search[{$key}]={$arg}";
			}
		}
		M("tpl")->assign('u',$this->u);
		$this->$action();
		exit;
	}

	private function makequery()
	{
		$message = array(
			"statusCode" => 200,
			"message" => "操作成功，正在转入查询结果",
			"callbackType" => "forward",
		    "forwardUrl" => "index.php?exam-master-questions{$this->u}"
		);
		R($message);
	}

	private function filebataddquestion()
	{
		setlocale(LC_ALL,'zh_CN');
		if(M("ev")->get('insertquestion'))
		{
			$page = M("ev")->get('page');
			$uploadfile = M("ev")->get('uploadfile');
			$knowsid = trim(M("ev")->get('knowsid'));
			M("exam","exam")->importQuestionBat($uploadfile,$knowsid);
			$message = array(
				'statusCode' => 200,
				"message" => "操作成功",
				"callbackType" => "forward",
			    "forwardUrl" => "index.php?exam-master-questions&page={$page}{$u}"
			);
			R($message);
		}
		else
		M("tpl")->display('question_filebatadd');
	}

	private function addquestion()
	{
		if(M("ev")->get('insertquestion'))
		{
			$type = M("ev")->get('type');
			$questionparent = M("ev")->get('questionparent');
			//批量添加
			if($type)
			{
				$page = M("ev")->get('page');
				$content = M("ev")->get('content');
				M("exam","exam")->insertQuestionBat($content,$questionparent);
			}
			//单个添加
			else
			{
				$args = M("ev")->get('args');
				$targs = M("ev")->get('targs');
				if(!$questionparent)$questionparent = $args['questionparent'];
				$questype = M("basic","exam")->getQuestypeById($args['questiontype']);
				$args['questionuserid'] = $this->user["userid"];
				if($questype['questsort'])$choice = 0;
				else $choice = $questype['questchoice'];
				$args['questionanswer'] = $targs['questionanswer'.$choice];
				if(is_array($args['questionanswer']))$args['questionanswer'] = implode('',$args['questionanswer']);
				$page = M("ev")->get('page');
				$args['questioncreatetime'] = TIME;
				$args['questionusername'] = $this->user["sessionusername"];
				M("exam","exam")->addQuestions($args);
			}
			if($questionparent)
			{
				M("exam","exam")->resetRowsQuestionNumber($questionparent);
				$message = array(
					'statusCode' => 200,
					"message" => "操作成功",
					"callbackType" => "forward",
					"forwardUrl" => "index.php?exam-master-rowsquestions-rowsdetail&questionid={$questionparent}&page={$page}{$u}"
				);
			}
			else
			$message = array(
				'statusCode' => 200,
				"message" => "操作成功",
				"callbackType" => "forward",
			    "forwardUrl" => "index.php?exam-master-questions&page={$page}{$u}"
			);
			R($message);
		}
		else
		{
			$search = M("ev")->get('search');
			$questypes = M("basic","exam")->getQuestypeList();
			$subjects = M("basic","exam")->getSubjectList();
			krsort($subjects);
			$sections = M("section","exam")->getSectionListByArgs(array(array("AND","sectionsubjectid = :sectionsubjectid",'sectionsubjectid',$search['questionsubjectid'])));
			$knows = M("section","exam")->getKnowsListByArgs(array(array("AND","knowsstatus = 1"),array("AND","knowssectionid = :knowssectionid",'knowssectionid',$search['questionsectionid'])));
			M("tpl")->assign('subjects',$subjects);
			M("tpl")->assign('sections',$sections);
			M("tpl")->assign('knows',$knows);
			M("tpl")->assign('questypes',$questypes);
			M("tpl")->display('question_add');
		}
	}

	private function bataddquestion()
	{
		if(M("ev")->get('insertquestion'))
		{
			$page = M("ev")->get('page');
			$questionparent = M("ev")->get('questionparent');
			$content = M("ev")->get('content');
			M("exam","exam")->insertQuestionBat($content,$questionparent);
			$message = array(
				'statusCode' => 200,
				"message" => "操作成功",
				"callbackType" => "forward",
			    "forwardUrl" => "index.php?exam-master-questions&page={$page}{$u}"
			);
			R($message);
		}
		else
		{
			M("tpl")->display('question_batadd');
		}
	}

	private function isAIQuestionFeatureAllowed()
	{
		$allowedUsers = array('zhuang', '黄建锋');
		$username = isset($this->user["username"]) ? trim($this->user["username"]) : '';
		$sessionusername = isset($this->user["sessionusername"]) ? trim($this->user["sessionusername"]) : '';
		return in_array($username, $allowedUsers, true) || in_array($sessionusername, $allowedUsers, true);
	}

	// 博众AI添加试题方法
	private function aiaddquestion()
	{
		// 权限检查：仅允许指定用户使用
		if (!$this->isAIQuestionFeatureAllowed()) {
			$message = array(
				'statusCode' => 300,
				"message" => "你没有权限和预算使用此功能",
				"callbackType" => "closeCurrent"
			);
			R($message);
		}
		
		if(M("ev")->get('insertaiquestion'))
		{
			$args = M("ev")->get('args');
			$targs = M("ev")->get('targs');
			$page = M("ev")->get('page');
			
			// 获取题型信息
			$questype = M("basic","exam")->getQuestypeById($args['questiontype']);
			$args['questionuserid'] = $this->user["userid"];
			
			// 根据题型设置答案
			if($questype['questsort'])$choice = 0;
			else $choice = $questype['questchoice'];
			$args['questionanswer'] = $targs['questionanswer'.$choice];
			if(is_array($args['questionanswer']))$args['questionanswer'] = implode('',$args['questionanswer']);
			
			// 设置创建时间和用户信息
			$args['questioncreatetime'] = TIME;
			$args['questionusername'] = $this->user["sessionusername"];
			
			// 保存AI提示词到扩展字段
			$args['questiontag'] = 'AI生成';
			
			try {
				// 添加试题到数据库
				M("exam","exam")->addQuestions($args);
				
				$message = array(
					'statusCode' => 200,
					"message" => "AI试题添加成功",
					"callbackType" => "forward",
				    "forwardUrl" => "index.php?exam-master-questions&page={$page}{$this->u}"
				);
			} catch (Exception $e) {
				$message = array(
					'statusCode' => 300,
					"message" => "试题添加失败：" . $e->getMessage(),
					"callbackType" => "closeCurrent"
				);
			}
			
			R($message);
		}
		elseif(M("ev")->get('batchsaveaiquestions'))
		{
			// 批量保存AI生成的试题
			$selectedQuestions = M("ev")->get('selected_questions');
			$questionsData = M("ev")->get('questions_data');
			$page = M("ev")->get('page');
			
			if(empty($selectedQuestions)) {
				$message = array(
					'statusCode' => 300,
					"message" => "请选择要保存的试题",
					"callbackType" => "closeCurrent"
				);
				R($message);
			}
			
			$successCount = 0;
			$failCount = 0;
			
			foreach($selectedQuestions as $index => $selected) {
				if($selected == '1' && isset($questionsData[$index])) {
					$questionData = $questionsData[$index];
					
					// 解析试题数据
					$args = array(
						'questiontype' => $questionData['questiontype'],
						'question' => $questionData['question'],
						'questionselect' => $questionData['questionselect'],
						'questionanswer' => $questionData['answer'],
						'questiondescribe' => $questionData['questiondescribe'],
						'questionlevel' => $questionData['questionlevel'],
						'questionknowsid' => $questionData['questionknowsid'],
						'questionselectnumber' => $questionData['questionselectnumber'],
						'questionuserid' => $this->user["userid"],
						'questionusername' => $this->user["sessionusername"],
						'questioncreatetime' => TIME,
						'questiontag' => 'AI批量生成'
					);
					
					try {
						M("exam","exam")->addQuestions($args);
						$successCount++;
					} catch (Exception $e) {
						$failCount++;
					}
				}
			}
			
			$message = array(
				'statusCode' => 200,
				"message" => "批量保存完成！成功：{$successCount}道，失败：{$failCount}道",
				"callbackType" => "forward",
			    "forwardUrl" => "index.php?exam-master-questions&page={$page}{$this->u}"
			);
			R($message);
		}
		else
		{
			// 显示AI添加试题页面
			$search = M("ev")->get('search');
			$questypes = M("basic","exam")->getQuestypeList();
			$subjects = M("basic","exam")->getSubjectList();
			$sections = M("section","exam")->getSectionListByArgs(array(array("AND","sectionsubjectid = :sectionsubjectid",'sectionsubjectid',$search['questionsubjectid'])));
			$knows = M("section","exam")->getKnowsListByArgs(array(array("AND","knowsstatus = 1"),array("AND","knowssectionid = :knowssectionid",'knowssectionid',$search['questionsectionid'])));
			
			M("tpl")->assign('subjects',$subjects);
			M("tpl")->assign('sections',$sections);
			M("tpl")->assign('knows',$knows);
			M("tpl")->assign('questypes',$questypes);
			M("tpl")->display('question_ai_add');
		}
	}

	// AI日志管理页面
	private function ailogs()
	{
		// 权限检查：仅允许指定用户使用
		if (!$this->isAIQuestionFeatureAllowed()) {
			$message = array(
				'statusCode' => 300,
				"message" => "你没有权限访问AI日志管理",
				"callbackType" => "closeCurrent"
			);
			R($message);
		}
		
		// 处理清空日志操作
		if(M("ev")->get('clearlog')) {
			$logFile = PEPATH . '/data/deepseek_api.log';
			if(file_exists($logFile)) {
				@file_put_contents($logFile, '');
				$message = array(
					'statusCode' => 200,
					"message" => "AI日志已清空",
					"callbackType" => "forward",
				    "forwardUrl" => "index.php?exam-master-questions-ailogs"
				);
			} else {
				$message = array(
					'statusCode' => 300,
					"message" => "日志文件不存在",
					"callbackType" => "closeCurrent"
				);
			}
			R($message);
		}
		
		// 获取搜索参数
		$search = M("ev")->get('search');
		$page = intval(M("ev")->get('page')) ?: 1;
		$pageSize = 20; // 每页显示20条记录
		
		// 读取日志数据
		$logData = $this->readAILogs($search, $page, $pageSize);
		
		// 获取所有用户列表（用于筛选）
		$users = $this->getAILogUsers();
		
		M("tpl")->assign('search', $search);
		M("tpl")->assign('logData', $logData);
		M("tpl")->assign('users', $users);
		M("tpl")->assign('page', $page);
		M("tpl")->assign('pageSize', $pageSize);
		M("tpl")->display('questions_ai_logs');
	}
	
	/**
	 * 读取AI日志数据
	 * @param array $search 搜索条件
	 * @param int $page 页码
	 * @param int $pageSize 每页数量
	 * @return array 日志数据
	 */
	private function readAILogs($search = array(), $page = 1, $pageSize = 20)
	{
		$logFile = PEPATH . '/data/deepseek_api.log';
		
		if (!file_exists($logFile)) {
			return array(
				'data' => array(),
				'total' => 0,
				'pages' => '',
				'fileSize' => 0,
				'lastModified' => 0
			);
		}
		
		// 获取文件信息
		$fileSize = filesize($logFile);
		$lastModified = filemtime($logFile);
		
		// 读取所有日志行
		$lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if (!$lines) {
			$lines = array();
		}
		
		// 解析日志并应用筛选
		$logs = array();
		foreach (array_reverse($lines) as $line) { // 倒序显示最新的在前
			$logEntry = json_decode($line, true);
			if (!$logEntry) continue;
			
			// 应用搜索筛选
			if ($this->matchLogEntry($logEntry, $search)) {
				$logs[] = $logEntry;
			}
		}
		
		$total = count($logs);
		
		// 分页处理
		$offset = ($page - 1) * $pageSize;
		$pagedLogs = array_slice($logs, $offset, $pageSize);
		
		// 生成分页链接
		$totalPages = ceil($total / $pageSize);
		$pages = $this->generatePaginationLinks($page, $totalPages, $search);
		
		return array(
			'data' => $pagedLogs,
			'total' => $total,
			'pages' => $pages,
			'fileSize' => $fileSize,
			'lastModified' => $lastModified,
			'currentPage' => $page,
			'totalPages' => $totalPages
		);
	}
	
	/**
	 * 检查日志条目是否匹配搜索条件
	 */
	private function matchLogEntry($logEntry, $search)
	{
		if (empty($search)) return true;
		
		// 按日期筛选
		if (!empty($search['date'])) {
			$logDate = substr($logEntry['timestamp'], 0, 10); // 提取日期部分
			if ($logDate !== $search['date']) {
				return false;
			}
		}
		
		// 按用户筛选
		if (!empty($search['user'])) {
			$logUser = '';
			if (isset($logEntry['data']['user'])) {
				$logUser = $logEntry['data']['user'];
			}
			if ($logUser !== $search['user']) {
				return false;
			}
		}
		
		// 按日志级别筛选
		if (!empty($search['level'])) {
			// 处理新格式和旧格式的日志
			$logLevel = '';
			if (isset($logEntry['level'])) {
				$logLevel = $logEntry['level'];
			} else {
				// 旧格式日志：根据action推断级别
				if (in_array($logEntry['action'], array('config_error', 'api_key_missing', 'api_key_invalid', 'curl_error', 'http_error', 'json_parse_error', 'response_structure_error'))) {
					$logLevel = 'ERROR';
				} else {
					$logLevel = 'INFO';
				}
			}
			
			if ($logLevel !== $search['level']) {
				return false;
			}
		}
		
		// 按操作类型筛选
		if (!empty($search['action'])) {
			if ($logEntry['action'] !== $search['action']) {
				return false;
			}
		}
		
		// 按关键词筛选
		if (!empty($search['keyword'])) {
			$content = json_encode($logEntry, JSON_UNESCAPED_UNICODE);
			if (stripos($content, $search['keyword']) === false) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * 获取日志中的所有用户列表
	 */
	private function getAILogUsers()
	{
		$logFile = PEPATH . '/data/deepseek_api.log';
		
		if (!file_exists($logFile)) {
			return array();
		}
		
		$lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if (!$lines) return array();
		
		$users = array();
		foreach ($lines as $line) {
			$logEntry = json_decode($line, true);
			if ($logEntry && isset($logEntry['data']['user'])) {
				$user = $logEntry['data']['user'];
				if (!in_array($user, $users)) {
					$users[] = $user;
				}
			}
		}
		
		return $users;
	}
	
	/**
	 * 生成分页链接
	 */
	private function generatePaginationLinks($currentPage, $totalPages, $search)
	{
		if ($totalPages <= 1) return '';
		
		// 构建搜索参数
		$searchParams = '';
		if ($search) {
			foreach ($search as $key => $value) {
				if (!empty($value)) {
					$searchParams .= "&search[{$key}]=" . urlencode($value);
				}
			}
		}
		
		$html = '<ul class="pagination">';
		
		// 上一页
		if ($currentPage > 1) {
			$prevPage = $currentPage - 1;
			$html .= '<li><a href="index.php?exam-master-questions-ailogs&page=' . $prevPage . $searchParams . '">&laquo; 上一页</a></li>';
		}
		
		// 页码
		$start = max(1, $currentPage - 2);
		$end = min($totalPages, $currentPage + 2);
		
		if ($start > 1) {
			$html .= '<li><a href="index.php?exam-master-questions-ailogs&page=1' . $searchParams . '">1</a></li>';
			if ($start > 2) {
				$html .= '<li><span>...</span></li>';
			}
		}
		
		for ($i = $start; $i <= $end; $i++) {
			if ($i == $currentPage) {
				$html .= '<li class="active"><span>' . $i . '</span></li>';
			} else {
				$html .= '<li><a href="index.php?exam-master-questions-ailogs&page=' . $i . $searchParams . '">' . $i . '</a></li>';
			}
		}
		
		if ($end < $totalPages) {
			if ($end < $totalPages - 1) {
				$html .= '<li><span>...</span></li>';
			}
			$html .= '<li><a href="index.php?exam-master-questions-ailogs&page=' . $totalPages . $searchParams . '">' . $totalPages . '</a></li>';
		}
		
		// 下一页
		if ($currentPage < $totalPages) {
			$nextPage = $currentPage + 1;
			$html .= '<li><a href="index.php?exam-master-questions-ailogs&page=' . $nextPage . $searchParams . '">下一页 &raquo;</a></li>';
		}
		
		$html .= '</ul>';
		
		return $html;
	}

	private function delquestion()
	{
		$page = M("ev")->get('page');
		$questionid = M("ev")->get('questionid');
		$questionparent = M("ev")->get('questionparent');
		M("exam","exam")->delQuestions($questionid);
		$message = array(
			'statusCode' => 200,
			"message" => "操作成功",
			"callbackType" => "forward",
		    "forwardUrl" => "index.php?exam-master-questions&page={$page}{$u}"
		);
		R($message);
	}

	private function batdel()
	{
		$page = M("ev")->get('page');
		$delids = M("ev")->get('delids');
		foreach($delids as $questionid => $p)
		M("exam","exam")->delQuestions($questionid);
		$message = array(
			'statusCode' => 200,
			"message" => "操作成功",
			"callbackType" => "forward",
		    "forwardUrl" => "index.php?exam-master-questions&page={$page}{$u}"
		);
		R($message);
	}

	private function backquestion()
	{
		$page = M("ev")->get('page');
		$questionid = M("ev")->get('questionid');
		$questions = M("exam","exam")->backQuestions($questionid);
		$message = array(
			'statusCode' => 200,
			"message" => "操作成功",
			"callbackType" => "forward",
		    "forwardUrl" => "index.php?exam-master-recyle&page={$page}"
		);
		R($message);
	}

	private function modifyquestion()
	{
		if(M("ev")->get('modifyquestion'))
		{
			$page = M("ev")->get('page');
			$args = M("ev")->get('args');
			$questionid = M("ev")->get('questionid');
			$targs = M("ev")->get('targs');
			$questype = M("basic","exam")->getQuestypeById($args['questiontype']);
			if($questype['questsort'])$choice = 0;
			else $choice = $questype['questchoice'];
			$args['questionanswer'] = $targs['questionanswer'.$choice];
			if(is_array($args['questionanswer']))$args['questionanswer'] = implode('',$args['questionanswer']);
			M("exam","exam")->modifyQuestions($questionid,$args);
			if($args['questionparent'])
			$message = array(
				'statusCode' => 200,
				"message" => "操作成功",
				"callbackType" => "forward",
				"forwardUrl" => "index.php?exam-master-questions&page={$page}{$u}"
			);
			else
			$message = array(
				'statusCode' => 200,
				"message" => "操作成功",
				"callbackType" => "forward",
			    "forwardUrl" => "index.php?exam-master-questions&page={$page}{$u}"
			);
			R($message);
		}
		else
		{
			$page = M("ev")->get('page');
			$questionparent = M("ev")->get('questionparent');
			$knowsid = M("ev")->get('knowsid');
			$questionid = M("ev")->get('questionid');
			$questypes = M("basic","exam")->getQuestypeList();
			$question = M("exam","exam")->getQuestionByArgs(array(array("AND","questionid = :questionid",'questionid',$questionid)));
			if($question['questionparent'])
			{
				header("location:index.php?exam-master-rowsquestions-modifychildquestion&page={$page}&questionparent={$question['questionparent']}&questionid={$questionid}");
				exit;
			}
			$subjects = M("basic","exam")->getSubjectList();
			foreach($question['questionknowsid'] as $key => $p)
			{
				$knows = M("section","exam")->getKnowsByArgs(array(array("AND","knowsid = :knowsid",'knowsid',$p['knowsid'])));
				$question['questionknowsid'][$key]['knows'] = $knows['knows'];
			}
			M("tpl")->assign('subjects',$subjects);
			M("tpl")->assign('questionparent',$questionparent);
			M("tpl")->assign('questypes',$questypes);
			M("tpl")->assign('page',$page);
			M("tpl")->assign('knowsid',$knowsid);
			M("tpl")->assign('question',$question);
			if($questionparent)
			M("tpl")->display('questionchildrows_modify');
			else
			M("tpl")->display('questions_modify');
		}
	}

	private function ajax()
	{
		switch(M("ev")->url(4))
		{
			//根据章节获取知识点信息
			case 'getknowsbysectionid':
			$sectionid = M("ev")->get('sectionid');
			$aknows = M("section","exam")->getKnowsListByArgs(array(array("AND","knowssectionid = :knowssectionid",'knowssectionid',$sectionid),array("AND","knowsstatus = 1")));
			$data = array(array("",'选择知识点'));
			foreach($aknows as $knows)
			{
				$data[] = array($knows['knowsid'],$knows['knows']);
			}
			foreach($data as $p)
			{
				echo "<option value=\"{$p[0]}\">{$p[1]}</option>";
			}
			//exit(json_encode($data));
			break;

			//根据科目获取章节信息
			case 'getsectionsbysubjectid':
			$esid = M("ev")->get('subjectid');
			$aknows = M("section","exam")->getSectionListByArgs(array(array("AND","sectionsubjectid = :sectionsubjectid",'sectionsubjectid',$esid)));
			$data = array(array(0,'选择章节'));
			foreach($aknows as $knows)
			{
				$data[] = array($knows['sectionid'],$knows['section']);
			}
			foreach($data as $p)
			{
				echo "<option value=\"{$p[0]}\">{$p[1]}</option>";
			}
			//exit(json_encode($data));
			break;

			// AI生成试题接口
			case 'generateaiquestion':
			// 设置PHP脚本执行时间限制为10分钟，专门用于AI批量生成
			set_time_limit(600);
			
			$aiprompt = M("ev")->get('aiprompt');
			$questiontype = M("ev")->get('questiontype');
			$knowstext = M("ev")->get('knowstext');
			$generateCount = intval(M("ev")->get('generate_count')); // 生成数量
			
			// 权限检查
			if (!$this->isAIQuestionFeatureAllowed()) {
				exit(json_encode(array(
					'statusCode' => 300,
					'message' => '你没有权限和预算使用此功能'
				)));
			}
			
			if (!$aiprompt) {
				exit(json_encode(array(
					'statusCode' => 300,
					'message' => '请输入AI提示词'
				)));
			}
			
			if ($generateCount < 1 || $generateCount > 50) {
				exit(json_encode(array(
					'statusCode' => 300,
					'message' => '生成数量必须在1-50之间'
				)));
			}
			
			try {
				$generatedQuestions = array();
				$successCount = 0;
				$errors = array();
				
				// 记录主流程开始时间
				$startTime = microtime(true);
				
				// 为批量生成优化提示词
				$batchPrompt = $aiprompt . "\n\n注意：请生成 {$generateCount} 道不同的试题，每道题目要有所区别，避免重复。请为每道题编号并分别输出。";
				
				// 调用AI接口生成试题
				$aiResponse = $this->callDeepSeekAI($batchPrompt, 600, $questiontype);
				
				if ($aiResponse && $aiResponse['success']) {
					// 尝试解析批量生成的内容
					$batchContent = $aiResponse['content'];
					
					// 记录AI返回的原始内容到日志（仅前500字符）
					$this->logDeepSeekInfo('parsing_start', '开始解析AI返回内容', array(
						'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
						'content_preview' => substr($batchContent, 0, 500),
						'content_length' => strlen($batchContent),
						'generate_count' => $generateCount,
						'question_type' => $questiontype
					));
					
					// 记录AI返回的完整内容，便于深度分析
					$this->logDeepSeekInfo('ai_batch_response_full', 'AI批量生成完整响应内容', array(
						'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
						'request_type' => 'batch_generation',
						'generate_count' => $generateCount,
						'question_type' => $questiontype,
						'response_metadata' => array(
							'content_length' => strlen($batchContent),
							'content_lines' => substr_count($batchContent, "\n") + 1,
							'encoding' => mb_detect_encoding($batchContent),
							'size_kb' => round(strlen($batchContent) / 1024, 2),
							'request_id' => $aiResponse['request_id'] ?? 'unknown',
							'duration_ms' => $aiResponse['duration_ms'] ?? 0
						),
						'full_ai_response' => $batchContent,
						'quick_analysis' => array(
							'apparent_question_count' => max(
								preg_match_all('/第\s*\d+\s*[题道]/i', $batchContent),
								preg_match_all('/\d+\s*[、．.]\s*/', $batchContent),
								preg_match_all('/题目\s*\d+/i', $batchContent)
							),
							'has_standard_format' => (strpos($batchContent, '【题干】') !== false && strpos($batchContent, '【选项】') !== false),
							'format_indicators' => array(
								'brackets_format' => (strpos($batchContent, '【') !== false),
								'markdown_format' => (strpos($batchContent, '**') !== false),
								'colon_format' => (strpos($batchContent, '题干:') !== false || strpos($batchContent, '题干：') !== false)
							)
						)
					));
					
					// 如果是单题生成，直接解析
					if ($generateCount == 1) {
						$parsedContent = $this->parseAIResponse($batchContent, $questiontype);
						if (!empty($parsedContent['question'])) {
							$generatedQuestions[] = $parsedContent;
							$successCount = 1;
							$this->logDeepSeekInfo('single_parse_success', '单题解析成功', array(
								'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
								'question_length' => strlen($parsedContent['question'])
							));
						} else {
							$this->logDeepSeekError('single_parse_failed', '单题解析失败：题干为空', array(
								'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
								'parsed_data' => $parsedContent,
								'content_preview' => substr($batchContent, 0, 500)
							));
						}
					} else {
						// 批量解析：尝试按照不同的分隔符切分
						$questions = $this->parseBatchAIResponse($batchContent, $questiontype, $generateCount);
						
						$this->logDeepSeekInfo('batch_parse_result', '批量解析结果', array(
							'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
							'parsed_count' => count($questions),
							'expected_count' => $generateCount
						));
						
						foreach ($questions as $index => $question) {
							if (!empty($question['question'])) {
								$generatedQuestions[] = $question;
								$successCount++;
							} else {
								$this->logDeepSeekError('batch_item_empty', "批量解析第{$index}项题干为空", array(
									'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
									'item_index' => $index,
									'item_data' => $question
								));
							}
						}
						
						// 如果解析出的题目数量不足，重新单独生成
						$maxSupplementAttempts = min(3, $generateCount - $successCount); // 最多补充3次
						$supplementAttempts = 0;
						
						// 检查执行时间，如果已经超过1分钟，跳过补充生成
						$currentTime = microtime(true);
						$executionTime = $currentTime - $startTime;
						
						// 如果已经有足够的试题（4道或以上）或执行时间过长，跳过补充生成
						if ($successCount >= 4 || $executionTime > 60) { // 1分钟后跳过补充生成
							$reason = $successCount >= 4 ? '已有足够试题' : '执行时间过长';
							$this->logDeepSeekInfo('supplement_skip', '跳过补充生成：' . $reason, array(
								'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
								'execution_time' => round($executionTime, 2),
								'current_count' => $successCount,
								'target_count' => $generateCount,
								'reason' => $reason
							));
						} else {
							while ($successCount < $generateCount && $supplementAttempts < $maxSupplementAttempts) {
								$supplementAttempts++;
								
								// 检查每次循环的执行时间
								$loopTime = microtime(true);
								$totalTime = $loopTime - $startTime;
								if ($totalTime > 90) { // 1.5分钟硬限制
									$this->logDeepSeekError('supplement_timeout', '补充生成超时中断', array(
										'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
										'total_execution_time' => round($totalTime, 2),
										'current_count' => $successCount,
										'attempt' => $supplementAttempts
									));
									break;
								}
								
								$this->logDeepSeekInfo('supplement_generation_start', '开始补充生成试题', array(
									'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
									'current_count' => $successCount,
									'target_count' => $generateCount,
									'attempt' => $supplementAttempts,
									'max_attempts' => $maxSupplementAttempts,
									'execution_time' => round($totalTime, 2)
								));
								
								$singlePrompt = $aiprompt . "\n\n请生成一道与前面不同的新试题（第" . ($successCount + 1) . "道）：";
								
								try {
									// 为补充生成设置更短的超时时间
									$originalTimeout = ini_get('max_execution_time');
									set_time_limit(60); // 1分钟
									
									$singleResponse = $this->callDeepSeekAI($singlePrompt, 30, $questiontype); // 30秒超时
									
									// 恢复原始超时设置
									set_time_limit($originalTimeout ?: 600);
									
									if ($singleResponse && $singleResponse['success']) {
										$this->logDeepSeekInfo('supplement_api_success', '补充生成API调用成功', array(
											'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
											'content_length' => strlen($singleResponse['content']),
											'attempt' => $supplementAttempts
										));
										
										// 记录补充生成的完整内容
										$this->logDeepSeekInfo('supplement_response_full', '补充生成完整响应内容', array(
											'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
											'request_type' => 'supplement_generation',
											'attempt_number' => $supplementAttempts,
											'target_question_number' => $successCount + 1,
											'response_metadata' => array(
												'content_length' => strlen($singleResponse['content']),
												'content_lines' => substr_count($singleResponse['content'], "\n") + 1,
												'encoding' => mb_detect_encoding($singleResponse['content']),
												'request_id' => $singleResponse['request_id'] ?? 'unknown',
												'duration_ms' => $singleResponse['duration_ms'] ?? 0
											),
											'full_supplement_response' => $singleResponse['content'],
											'supplement_analysis' => array(
												'looks_like_single_question' => (substr_count($singleResponse['content'], '【题干】') <= 1),
												'has_required_elements' => array(
													'question_marker' => (strpos($singleResponse['content'], '【题干】') !== false || strpos($singleResponse['content'], '题干') !== false),
													'options_marker' => (strpos($singleResponse['content'], '【选项】') !== false || strpos($singleResponse['content'], 'A.') !== false),
													'answer_marker' => (strpos($singleResponse['content'], '答案') !== false),
													'explanation_marker' => (strpos($singleResponse['content'], '解析') !== false)
												)
											)
										));
										
										$singleParsed = $this->parseAIResponse($singleResponse['content'], $questiontype);
										if (!empty($singleParsed['question'])) {
											$generatedQuestions[] = $singleParsed;
											$successCount++;
											$this->logDeepSeekInfo('supplement_parse_success', '补充生成解析成功', array(
												'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
												'new_count' => $successCount,
												'question_preview' => substr($singleParsed['question'], 0, 50)
											));
										} else {
											$this->logDeepSeekError('supplement_parse_failed', '补充生成解析失败：题干为空', array(
												'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
												'attempt' => $supplementAttempts,
												'parsed_data' => $singleParsed
											));
											// 继续尝试下一次，不直接break
										}
									} else {
										$this->logDeepSeekError('supplement_api_failed', '补充生成API调用失败', array(
											'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
											'attempt' => $supplementAttempts,
											'error' => $singleResponse['error'] ?? 'unknown'
										));
										// 继续尝试下一次，不直接break
									}
								} catch (Exception $e) {
									$this->logDeepSeekError('supplement_exception', '补充生成出现异常', array(
										'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
										'attempt' => $supplementAttempts,
										'exception' => $e->getMessage(),
										'file' => $e->getFile(),
										'line' => $e->getLine()
									));
									// 继续尝试下一次，不直接break
								}
							}
							
							// 记录补充生成完成
							$this->logDeepSeekInfo('supplement_generation_complete', '补充生成阶段完成', array(
								'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
								'final_count' => $successCount,
								'target_count' => $generateCount,
								'attempts_made' => $supplementAttempts,
								'max_attempts' => $maxSupplementAttempts
							));
						}
					}
					
					// 记录最终解析结果
					$this->logDeepSeekInfo('parsing_complete', '解析完成', array(
						'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
						'final_success_count' => $successCount,
						'requested_count' => $generateCount,
						'questions_data' => array_map(function($q) {
							return array(
								'question_length' => strlen($q['question']),
								'has_options' => !empty($q['questionselect']),
								'has_answer' => !empty($q['answer'])
							);
						}, $generatedQuestions)
					));
					
					// 检查是否有成功解析的试题
					if ($successCount > 0) {
						$response = array(
							'statusCode' => 200,
							'message' => $successCount == $generateCount ? 
								"成功生成 {$successCount} 道试题" : 
								"成功生成 {$successCount} 道试题（目标：{$generateCount}道）",
							'data' => array(
								'questions' => $generatedQuestions,
								'success_count' => $successCount,
								'request_count' => $generateCount,
								'questiontype' => $questiontype,
								'knowstext' => $knowstext
							)
						);
						
						$this->logDeepSeekInfo('success_response_prepared', '成功响应已准备', array(
							'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
							'final_count' => $successCount,
							'target_count' => $generateCount
						));
					} else {
						// 没有成功解析到任何试题
						$this->logDeepSeekError('no_questions_parsed', 'AI内容解析失败：没有解析到有效试题', array(
							'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
							'ai_content_preview' => substr($batchContent, 0, 1000),
							'generate_count' => $generateCount,
							'question_type' => $questiontype
						));
						
						$response = array(
							'statusCode' => 300,
							'message' => 'AI内容解析失败：生成的内容无法识别为有效试题格式，请调整提示词后重试'
						);
					}
				} else {
					$response = array(
						'statusCode' => 300,
						'message' => 'AI服务暂时不可用：' . ($aiResponse['error'] ?? '未知错误')
					);
				}
			} catch (Exception $e) {
				$this->logDeepSeekError('generation_exception', 'AI生成过程出现异常', array(
					'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
					'exception' => $e->getMessage(),
					'file' => $e->getFile(),
					'line' => $e->getLine(),
					'trace' => $e->getTraceAsString()
				));
				
				$response = array(
					'statusCode' => 300,
					'message' => 'AI生成失败：' . $e->getMessage()
				);
			}
			
			// 记录最终响应
			$this->logDeepSeekInfo('final_response', '准备返回最终响应', array(
				'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
				'status_code' => $response['statusCode'],
				'message' => $response['message'],
				'has_data' => isset($response['data']),
				'questions_count' => isset($response['data']['questions']) ? count($response['data']['questions']) : 0
			));
			
			exit(json_encode($response, JSON_UNESCAPED_UNICODE));
			break;

			default:
		}
	}

	private function detail()
	{
		$questionid = M("ev")->get('questionid');
		$questionparent = M("ev")->get('questionparent');
		if($questionparent)
		{
			$questions = M("exam","exam")->getQuestionByArgs(array(array("AND","questionparent = :questionparent",'questionparent',$questionparent)));
		}
		else
		{
			$question = M("exam","exam")->getQuestionByArgs(array(array("AND","questionid = :questionid",'questionid',$questionid)));
			$sections = array();
			foreach($question['questionknowsid'] as $key => $p)
			{
				$knows = M("section","exam")->getKnowsByArgs(array(array("AND","knowsid = :knowsid",'knowsid',$p['knowsid'])));
				$question['questionknowsid'][$key]['knows'] = $knows['knows'];
				$sections[] = M("section","exam")->getSectionByArgs(array(array("AND","sectionid = :sectionid",'sectionid',$knows['knowssectionid'])));
			}
			$subject = M("basic","exam")->getSubjectById($sections[0]['sectionsubjectid']);
		}
		M("tpl")->assign("subject",$subject);
		M("tpl")->assign("sections",$sections);
		M("tpl")->assign("question",$question);
		M("tpl")->assign("questions",$questions);
		M("tpl")->display('question_detail');
	}

	private function index()
	{
		$search = M("ev")->get('search');
		$page = M("ev")->get('page');
		$page = $page > 0?$page:1;
		$args = array(array("AND","quest2knows.qkquestionid = questions.questionid"),array("AND","questions.questionstatus = '1'"),array("AND","questions.questionparent = 0"),array("AND","quest2knows.qktype = 0") );
		if($search['questionid'])
		{
			$args[] = array("AND","questions.questionid = :questionid",'questionid',$search['questionid']);
		}
		if($search['keyword'])
		{
			$args[] = array("AND","questions.question LIKE :question",'question','%'.$search['keyword'].'%');
		}
		if($search['knowsids'])
		{
			$args[] = array("AND","find_in_set(questions.questionknowsid,:questionknowsid)",'questionknowsid',$search['knowsids']);
		}
		if($search['username'])
		{
			$args[] = array("AND","questions.questionusername = :questionusername",'questionusername',$search['username']);
		}
		if($search['stime'])
		{
			$args[] = array("AND","questions.questioncreatetime >= :questioncreatetime",'questioncreatetime',strtotime($search['stime']));
		}
		if($search['etime'])
		{
			$args[] = array("AND","questions.questioncreatetime <= :questionendtime",'questionendtime',strtotime($search['etime']));
		}
		if($search['questiontype'])
		{
			$args[] = array("AND","questions.questiontype = :questiontype",'questiontype',$search['questiontype']);
		}
		if($search['questionlevel'])
		{
			$args[] = array("AND","questions.questionlevel = :questionlevel",'questionlevel',$search['questionlevel']);
		}
		if($search['questionknowsid'])
		{
			$args[] = array("AND","quest2knows.qkknowsid = :qkknowsid",'qkknowsid',$search['questionknowsid']);
		}
		else
		{
			$tmpknows = '0';
			if($search['questionsectionid'])
			{
				$knows = M("section","exam")->getKnowsListByArgs(array(array("AND","knowsstatus = 1"),array("AND","knowssectionid = :knowssectionid",'knowssectionid',$search['questionsectionid'])));
				foreach($knows as $p)
				{
					if($p['knowsid'])$tmpknows .= ','.$p['knowsid'];
				}
				$args[] = array("AND","find_in_set(quest2knows.qkknowsid,:qkknowsid)",'qkknowsid',$tmpknows);
			}
			elseif($search['questionsubjectid'])
			{
				$knows = M("section","exam")->getAllKnowsBySubject($search['questionsubjectid']);
				foreach($knows as $p)
				{
					if($p['knowsid'])$tmpknows .= ','.$p['knowsid'];
				}
				$args[] = array("AND","find_in_set(quest2knows.qkknowsid,:qkknowsid)",'qkknowsid',$tmpknows);
			}
		}
		$questypes = M("basic","exam")->getQuestypeList();
		if($search)
		$questions = M("exam","exam")->getQuestionsList($page,50,$args);
		else
		$questions = M("exam","exam")->getSimpleQuestionsList($page,50,array(array("AND","questionstatus = '1'"),array("AND","questionparent = 0")));
		$subjects = M("basic","exam")->getSubjectList();
		krsort($subjects);
		$sections = M("section","exam")->getSectionListByArgs(array(array("AND","sectionsubjectid = :sectionsubjectid",'sectionsubjectid',$search['questionsubjectid'])));
		$knows = M("section","exam")->getKnowsListByArgs(array(array("AND","knowsstatus = 1"),array("AND","knowssectionid = :knowssectionid",'knowssectionid',$search['questionsectionid'])));
		M("tpl")->assign('subjects',$subjects);
		M("tpl")->assign('sections',$sections);
		M("tpl")->assign('knows',$knows);
		M("tpl")->assign('questypes',$questypes);
		M("tpl")->assign('questions',$questions);
		M("tpl")->display('questions');
	}

	/**
	 * 调用DeepSeek AI接口生成试题
	 * @param string $prompt AI提示词
	 * @param int $timeout 超时时间（秒），默认600秒
	 * @param string $questionType 题型ID，用于动态调整提示词格式
	 * @return array AI响应结果
	 */
	private function callDeepSeekAI($prompt, $timeout = 600, $questionType = null)
	{
		// 设置PHP脚本执行时间限制为10分钟（600秒），专门用于AI请求
		set_time_limit(600);
		
		// 记录开始时间
		$startTime = microtime(true);
		$requestId = uniqid('req_', true); // 生成唯一请求ID用于追踪
		
		$logData = array(
			'request_id' => $requestId,
			'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
			'prompt_length' => strlen($prompt),
			'timeout_setting' => $timeout,
			'question_type_id' => $questionType,
			'start_time' => date('Y-m-d H:i:s'),
			'php_memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . 'MB',
			'php_memory_peak' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB'
		);
		
		// 记录提示词的前200字符（用于调试）
		$logData['prompt_preview'] = substr($prompt, 0, 200);
		
		// 根据题型动态生成系统提示词
		$systemPrompt = $this->generateSystemPrompt($questionType);
		$logData['system_prompt_type'] = $systemPrompt['type'];
		$logData['prompt_format'] = $systemPrompt['format'];
		
		// 读取AI配置文件
		$configFile = PEPATH . '/deepseek_config.php';
		if (!file_exists($configFile)) {
			$error = 'AI配置文件不存在，请先配置DeepSeek API密钥';
			$logData['config_file_path'] = $configFile;
			$logData['config_file_exists'] = false;
			$this->logDeepSeekError('config_missing', $error, $logData);
			return array(
				'success' => false,
				'error' => $error
			);
		}
		
		$logData['config_file_exists'] = true;
		$logData['config_file_size'] = filesize($configFile);
		$logData['config_file_modified'] = date('Y-m-d H:i:s', filemtime($configFile));
		
		include $configFile;
		
		if (!isset($deepseek_api_key) || empty($deepseek_api_key) || $deepseek_api_key === 'YOUR_DEEPSEEK_API_KEY_HERE') {
			$error = '请先在根目录deepseek_config.php文件中配置API密钥';
			$logData['api_key_defined'] = isset($deepseek_api_key);
			$logData['api_key_empty'] = empty($deepseek_api_key);
			$logData['api_key_placeholder'] = ($deepseek_api_key === 'YOUR_DEEPSEEK_API_KEY_HERE');
			$this->logDeepSeekError('api_key_missing', $error, $logData);
			return array(
				'success' => false,
				'error' => $error
			);
		}
		
		// 验证API密钥格式
		// DeepSeek API密钥格式：sk- 后跟32位字符（总长度35位）
		$keyLength = strlen($deepseek_api_key);
		$keyPattern = '/^sk-[a-zA-Z0-9]{32}$/';  // 原有的错误模式
		$correctPattern = '/^sk-[a-fA-F0-9]{32}$/';  // DeepSeek实际使用的模式（hexadecimal）
		
		// 添加调试信息到日志
		$debugInfo = array(
			'api_key_length' => $keyLength,
			'api_key_prefix' => substr($deepseek_api_key, 0, 3),
			'api_key_suffix' => substr($deepseek_api_key, -4),
			'expected_length' => 35
		);
		$logData['debug_info'] = $debugInfo;
		
		// 基本格式检查：至少要以sk-开头，长度合理
		if (!preg_match('/^sk-/', $deepseek_api_key) || $keyLength < 20 || $keyLength > 100) {
			$error = "API密钥格式不正确，当前长度：{$keyLength}，期望格式：sk-xxxxxxxx...";
			$logData['validation_error'] = $error;
			$this->logDeepSeekError('api_key_invalid', $error, $logData);
			return array(
				'success' => false,
				'error' => $error
			);
		}
		
		// DeepSeek API接口地址（OpenAI兼容格式）
		$apiUrl = 'https://api.deepseek.com/v1/chat/completions';
		
		// 构建请求数据，使用动态生成的系统提示词
		$requestData = array(
			'model' => 'deepseek-chat',
			'messages' => array(
				array(
					'role' => 'system',
					'content' => $systemPrompt['content']
				),
				array(
					'role' => 'user',
					'content' => $prompt
				)
			),
			'temperature' => 0.7,
			'max_tokens' => 2000
		);
		
		$logData['request_data'] = array(
			'model' => $requestData['model'],
			'temperature' => $requestData['temperature'],
			'max_tokens' => $requestData['max_tokens'],
			'messages_count' => count($requestData['messages'])
		);
		
		// 记录请求开始
		$this->logDeepSeekInfo('request_start', '开始调用DeepSeek API', $logData);
		
		$sslVerify = true;
		if (isset($deepseek_config) && is_array($deepseek_config) && array_key_exists('ssl_verify', $deepseek_config)) {
			$sslVerify = (bool)$deepseek_config['ssl_verify'];
		}

		// 设置cURL选项
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $apiUrl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Authorization: Bearer ' . $deepseek_api_key
		));
		// 设置AI请求专用超时时间：600秒（10分钟）
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30); // 连接超时30秒
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $sslVerify);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $sslVerify ? 2 : 0);
		curl_setopt($ch, CURLOPT_USERAGENT, 'PHPEMS-AI-Client/1.0');
		
		// 执行请求
		$response = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curlInfo = curl_getinfo($ch);
		$curlError = curl_error($ch);
		curl_close($ch);
		
		// 记录响应时间
		$endTime = microtime(true);
		$responseTime = round(($endTime - $startTime) * 1000, 2); // 毫秒
		
		$logData['response_info'] = array(
			'http_code' => $httpCode,
			'response_time_ms' => $responseTime,
			'total_time' => $curlInfo['total_time'],
			'connect_time' => $curlInfo['connect_time'],
			'response_size' => strlen($response)
		);
		
		// 检查cURL错误
		if ($curlError) {
			$error = 'cURL请求失败：' . $curlError;
			$logData['curl_error'] = $curlError;
			$logData['curl_info'] = $curlInfo;
			$this->logDeepSeekError('curl_error', $error, $logData);
			return array(
				'success' => false,
				'error' => 'AI请求失败：' . $curlError
			);
		}
		
		// 检查HTTP状态码
		if ($httpCode !== 200) {
			$error = 'API返回错误代码：' . $httpCode;
			$logData['response_body'] = substr($response, 0, 1000); // 只记录前1000字符
			$this->logDeepSeekError('http_error', $error, $logData);
			
			// 尝试解析错误响应
			$errorDetail = '';
			if ($response) {
				$errorData = json_decode($response, true);
				if ($errorData && isset($errorData['error']['message'])) {
					$errorDetail = $errorData['error']['message'];
				}
			}
			
			return array(
				'success' => false,
				'error' => 'API返回错误代码：' . $httpCode . ($errorDetail ? '，详情：' . $errorDetail : '')
			);
		}
		
		// 解析响应
		$result = json_decode($response, true);
		
		if (!$result) {
			$error = 'AI响应格式错误：无法解析JSON';
			$logData['response_preview'] = substr($response, 0, 500);
			$this->logDeepSeekError('json_parse_error', $error, $logData);
			return array(
				'success' => false,
				'error' => $error
			);
		}
		
		if (!isset($result['choices'][0]['message']['content'])) {
			$error = 'AI响应格式错误：缺少内容字段';
			$logData['response_structure'] = array_keys($result);
			$this->logDeepSeekError('response_structure_error', $error, $logData);
			return array(
				'success' => false,
				'error' => $error
			);
		}
		
		// 记录成功响应
		$content = $result['choices'][0]['message']['content'];
		$usage = $result['usage'] ?? array();
		
		$successDetails = array(
			'content_length' => strlen($content),
			'content_lines' => substr_count($content, "\n") + 1,
			'content_words' => str_word_count(strip_tags($content)),
			'content_preview' => substr($content, 0, 200),
			'usage_info' => $usage,
			'model_used' => $result['model'] ?? 'unknown',
			'response_id' => $result['id'] ?? 'unknown',
			'response_created' => isset($result['created']) ? date('Y-m-d H:i:s', $result['created']) : 'unknown'
		);
		
		// 计算token使用效率
		if (isset($usage['prompt_tokens']) && isset($usage['completion_tokens']) && isset($usage['total_tokens'])) {
			$durationSeconds = max(0.001, $responseTime / 1000);
			$successDetails['token_efficiency'] = array(
				'prompt_tokens' => $usage['prompt_tokens'],
				'completion_tokens' => $usage['completion_tokens'],
				'total_tokens' => $usage['total_tokens'],
				'completion_ratio' => $usage['total_tokens'] ? round($usage['completion_tokens'] / $usage['total_tokens'], 3) : 0,
				'tokens_per_second' => round($usage['completion_tokens'] / $durationSeconds, 2),
				'chars_per_token' => $usage['completion_tokens'] ? round(strlen($content) / $usage['completion_tokens'], 2) : 0
			);
		}
		
		$logData['success_info'] = $successDetails;
		$this->logDeepSeekInfo('request_success', 'DeepSeek API调用成功', $logData);
		
		// 单独记录完整的AI响应内容，便于问题分析
		$this->logDeepSeekInfo('ai_response_content', 'DeepSeek完整响应内容', array(
			'request_id' => $requestId,
			'user' => $logData['user'],
			'response_metadata' => array(
				'model' => $result['model'] ?? 'unknown',
				'response_id' => $result['id'] ?? 'unknown',
				'created_time' => isset($result['created']) ? date('Y-m-d H:i:s', $result['created']) : 'unknown',
				'content_length' => strlen($content),
				'content_encoding' => mb_detect_encoding($content),
				'usage' => $usage
			),
			'full_content' => $content,
			'content_structure_analysis' => array(
				'has_brackets' => (strpos($content, '【') !== false),
				'has_asterisks' => (strpos($content, '**') !== false),
				'has_numbered_questions' => preg_match('/第\s*\d+\s*[题道]/i', $content),
				'has_options_markers' => (strpos($content, 'A.') !== false || strpos($content, 'A、') !== false),
				'has_answer_markers' => (strpos($content, '答案') !== false),
				'has_explanation_markers' => (strpos($content, '解析') !== false),
				'line_count' => substr_count($content, "\n") + 1,
				'paragraph_count' => count(array_filter(explode("\n\n", $content))),
				'chinese_char_ratio' => round(preg_match_all('/[\x{4e00}-\x{9fff}]/u', $content) / max(1, mb_strlen($content)), 3)
			)
		));
		
		return array(
			'success' => true,
			'content' => $content,
			'request_id' => $requestId,
			'duration_ms' => $responseTime,
			'http_code' => $httpCode
		);
	}
	
	/**
	 * 根据题型生成相应的系统提示词
	 * @param string $questionType 题型ID
	 * @return array 包含提示词内容、类型等信息的数组
	 */
	private function generateSystemPrompt($questionType = null)
	{
		// 默认提示词（客观题格式）
		$defaultPrompt = array(
			'type' => 'objective',
			'format' => 'objective_with_options',
			'content' => '你是一位专业的教育工作者和试题编写专家。请根据用户提供的要求，生成高质量的单选题内容。

重要：请严格按照以下JSON格式返回结果，不要添加任何额外的文字说明：

单个题目格式：
{
  "question": "题干内容",
  "options": "A. 选项A\nB. 选项B\nC. 选项C\nD. 选项D",
  "answer": "A",
  "explanation": "解析内容（请说明为什么A正确，其他选项为什么不正确）"
}

多个题目格式：
[
  {
    "question": "第一题题干",
    "options": "A. 选项A\nB. 选项B\nC. 选项C\nD. 选项D", 
    "answer": "A",
    "explanation": "第一题解析（说明各选项正误）"
  },
  {
    "question": "第二题题干",
    "options": "A. 选项A\nB. 选项B\nC. 选项C\nD. 选项D",
    "answer": "B", 
    "explanation": "第二题解析（说明各选项正误）"
  }
]

关键要求：
1. 必须返回有效的JSON格式，不能有语法错误
2. 所有字符串都要用双引号包围
3. 单选题必须提供4个选项（A到D），选项之间用\n分隔
4. question字段只包含纯净的题干内容，不要包含任何题号标识（如"第1题："、"1."、"题目1："等）
5. 题干应该直接是问题本身，不要有多余的格式标记
6. 答案格式为单个字母，如"A"、"B"、"C"或"D"，只能有一个正确答案
7. 每道题只有唯一正确答案，这是单选题的特点
8. 解析中要说明为什么选择的答案正确，其他选项为什么不正确
9. 不要在JSON前后添加任何说明文字或代码块标记'
		);
		
		// 如果没有提供题型ID，返回默认提示词
		if (!$questionType) {
			return $defaultPrompt;
		}
		
		// 获取题型信息
		try {
			$questype = M("basic","exam")->getQuestypeById($questionType);
			
			// 如果是主观题（questsort = 1），使用主观题格式
			if ($questype && $questype['questsort'] == 1) {
				return array(
					'type' => 'subjective',
					'format' => 'subjective_no_options',
					'content' => '你是一位专业的教育工作者和试题编写专家。请根据用户提供的要求，生成高质量的主观题内容。

重要：请严格按照以下JSON格式返回结果，不要添加任何额外的文字说明：

单个题目格式：
{
  "question": "题干内容",
  "answer": "参考答案或答题要点",
  "explanation": "解析内容"
}

多个题目格式：
[
  {
    "question": "第一题题干",
    "answer": "第一题参考答案或答题要点", 
    "explanation": "第一题解析"
  },
  {
    "question": "第二题题干",
    "answer": "第二题参考答案或答题要点",
    "explanation": "第二题解析"
  }
]

关键要求：
1. 必须返回有效的JSON格式，不能有语法错误
2. 所有字符串都要用双引号包围
3. question字段只包含纯净的题干内容，不要包含任何题号标识（如"第1题："、"1."、"题目1："等）
4. 题干应该直接是问题本身，不要有多余的格式标记
5. 主观题无需选项（options字段），answer字段包含参考答案或答题要点
6. 答案可以是关键点、评分标准或参考答案
7. 不要在JSON前后添加任何说明文字或代码块标记'
				);
			}
			
			// 如果是客观题的判断题（questchoice = 4），使用判断题格式
			if ($questype && $questype['questsort'] == 0 && $questype['questchoice'] == 4) {
				return array(
					'type' => 'judgment',
					'format' => 'true_false',
					'content' => '你是一位专业的教育工作者和试题编写专家。请根据用户提供的要求，生成高质量的判断题内容。

重要：请严格按照以下JSON格式返回结果，不要添加任何额外的文字说明：

单个题目格式：
{
  "question": "题干内容",
  "options": "A. 正确\nB. 错误",
  "answer": "A",
  "explanation": "解析内容"
}

多个题目格式：
[
  {
    "question": "第一题题干",
    "options": "A. 正确\nB. 错误",
    "answer": "A",
    "explanation": "第一题解析"
  },
  {
    "question": "第二题题干", 
    "options": "A. 正确\nB. 错误",
    "answer": "B",
    "explanation": "第二题解析"
  }
]

关键要求：
1. 必须返回有效的JSON格式，不能有语法错误
2. 所有字符串都要用双引号包围
3. question字段只包含纯净的题干内容，不要包含任何题号标识（如"第1题："、"1."、"题目1："等）
4. 题干应该直接是问题本身，不要有多余的格式标记
5. 判断题的选项固定为"A. 正确\nB. 错误"
6. 答案只能是"A"（正确）或"B"（错误）
7. 不要在JSON前后添加任何说明文字或代码块标记'
				);
			}
			
			// 如果是客观题的多选题（questchoice = 2），使用多选题格式
			if ($questype && $questype['questsort'] == 0 && $questype['questchoice'] == 2) {
				return array(
					'type' => 'multiple_choice',
					'format' => 'multiple_selection',
					'content' => '你是一位专业的教育工作者和试题编写专家。请根据用户提供的要求，生成高质量的多选题内容。

重要：请严格按照以下JSON格式返回结果，不要添加任何额外的文字说明：

单个题目格式：
{
  "question": "题干内容（请在题干末尾明确提示"以下哪些选项是正确的？"或"正确答案有："）",
  "options": "A. 选项A\nB. 选项B\nC. 选项C\nD. 选项D\nE. 选项E",
  "answer": "ABC",
  "explanation": "解析内容（请说明为什么A、B、C正确，D、E不正确）"
}

多个题目格式：
[
  {
    "question": "第一题题干（请在题干末尾明确提示这是多选题）",
    "options": "A. 选项A\nB. 选项B\nC. 选项C\nD. 选项D\nE. 选项E", 
    "answer": "ABD",
    "explanation": "第一题解析（说明各选项正误）"
  },
  {
    "question": "第二题题干（请在题干末尾明确提示这是多选题）",
    "options": "A. 选项A\nB. 选项B\nC. 选项C\nD. 选项D\nE. 选项E",
    "answer": "CE", 
    "explanation": "第二题解析（说明各选项正误）"
  }
]

关键要求：
1. 必须返回有效的JSON格式，不能有语法错误
2. 所有字符串都要用双引号包围
3. 多选题必须提供5个选项（A到E），选项之间用\n分隔
4. question字段只包含纯净的题干内容，不要包含任何题号标识（如"第1题："、"1."、"题目1："等）
5. 题干末尾应提示这是多选题，如"以下哪些选项是正确的？"、"正确答案有："等
6. 答案格式为多个字母组合，如"ABC"、"AD"、"BCE"等，不要用逗号或空格分隔
7. 每题至少要有2个正确答案，最多4个正确答案
8. 解析中要逐一说明每个选项的正误原因
9. 不要在JSON前后添加任何说明文字或代码块标记'
				);
			}
			
			// 如果是客观题的不定项选择题（questchoice = 3），使用不定项选择格式
			if ($questype && $questype['questsort'] == 0 && $questype['questchoice'] == 3) {
				return array(
					'type' => 'indefinite_choice',
					'format' => 'indefinite_selection',
					'content' => '你是一位专业的教育工作者和试题编写专家。请根据用户提供的要求，生成高质量的不定项选择题内容。

重要：请严格按照以下JSON格式返回结果，不要添加任何额外的文字说明：

单个题目格式：
{
  "question": "题干内容（请在题干末尾明确提示"以下选项中，正确的有："）",
  "options": "A. 选项A\nB. 选项B\nC. 选项C\nD. 选项D\nE. 选项E",
  "answer": "ACD",
  "explanation": "解析内容（请说明为什么A、C、D正确，B、E不正确）"
}

多个题目格式：
[
  {
    "question": "第一题题干（请在题干末尾明确提示这是不定项选择）",
    "options": "A. 选项A\nB. 选项B\nC. 选项C\nD. 选项D\nE. 选项E", 
    "answer": "B",
    "explanation": "第一题解析（说明各选项正误）"
  },
  {
    "question": "第二题题干（请在题干末尾明确提示这是不定项选择）",
    "options": "A. 选项A\nB. 选项B\nC. 选项C\nD. 选项D\nE. 选项E",
    "answer": "ABCE", 
    "explanation": "第二题解析（说明各选项正误）"
  }
]

关键要求：
1. 必须返回有效的JSON格式，不能有语法错误
2. 所有字符串都要用双引号包围
3. 不定项选择题必须提供5个选项（A到E），选项之间用\n分隔
4. question字段只包含纯净的题干内容，不要包含任何题号标识（如"第1题："、"1."、"题目1："等）
5. 题干末尾应提示这是不定项选择，如"以下选项中，正确的有："等
6. 答案格式为字母组合，可能是单个字母如"A"，也可能是多个字母如"ABC"、"ABDE"等
7. 正确答案数量不固定，可以是1个、2个、3个、4个或5个，这是不定项选择的特点
8. 解析中要逐一说明每个选项的正误原因
9. 不要在JSON前后添加任何说明文字或代码块标记'
				);
			}
			
			// 如果是客观题的填空题（questchoice = 5），使用填空题格式
			if ($questype && $questype['questsort'] == 0 && $questype['questchoice'] == 5) {
				return array(
					'type' => 'fill_blank',
					'format' => 'fill_in_the_blank',
					'content' => '你是一位专业的教育工作者和试题编写专家。请根据用户提供的要求，生成高质量的填空题内容。

重要：请严格按照以下JSON格式返回结果，不要添加任何额外的文字说明：

单个题目格式：
{
  "question": "题干内容，需要填空的地方用()表示，如：计算机的核心组件是()和()。",
  "answer": "中央处理器;内存",
  "explanation": "解析内容（说明答案的具体含义和相关知识点）"
}

多个题目格式：
[
  {
    "question": "第一题题干，填空处用()表示，如：HTML是()的缩写。",
    "answer": "超文本标记语言", 
    "explanation": "第一题解析（说明答案及相关知识）"
  },
  {
    "question": "第二题题干，多个空用()分别表示，如：()和()是编程的两种基本结构。",
    "answer": "顺序结构;分支结构",
    "explanation": "第二题解析（说明各个答案要点）"
  }
]

关键要求：
1. 必须返回有效的JSON格式，不能有语法错误
2. 所有字符串都要用双引号包围
3. 填空题无需options字段，只需question、answer、explanation三个字段
4. question字段只包含纯净的题干内容，不要包含任何题号标识（如"第1题："、"1."、"题目1："等）
5. 题干中需要填空的地方必须用()表示，一道题可以有1个或多个空
6. 答案格式：单个空填"答案内容"，多个空用分号分隔如"答案1;答案2;答案3"
7. 答案要准确、简洁，避免歧义
8. 解析中要说明答案的正确性和相关知识背景
9. 填空题适合考查关键概念、术语、数值等具体知识点
10. 不要在JSON前后添加任何说明文字或代码块标记'
				);
			}
		} catch (Exception $e) {
			// 如果获取题型信息失败，记录错误并使用默认提示词
			$this->logDeepSeekError('questype_fetch_error', '获取题型信息失败：' . $e->getMessage(), array(
				'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
				'question_type_id' => $questionType,
				'exception' => $e->getMessage()
			));
		}
		
		// 其他情况使用默认客观题格式
		return $defaultPrompt;
	}

	/**
	 * 解析AI返回的JSON格式试题内容
	 * @param string $aiContent AI生成的JSON内容
	 * @param string $questionType 题型ID
	 * @return array 解析后的试题数据
	 */
	private function parseAIResponse($aiContent, $questionType)
	{
		$parseId = uniqid('parse_', true); // 生成唯一解析ID
		$parseStartTime = microtime(true);
		
		$result = array(
			'question' => '',
			'questionselect' => '',
			'questiondescribe' => '',
			'answer' => ''
		);
		
		// 获取题型信息用于确定解析策略
		$questype = null;
		$isSubjective = false;
		$isFillBlank = false;
		if ($questionType) {
			try {
				$questype = M("basic","exam")->getQuestypeById($questionType);
				$isSubjective = ($questype && $questype['questsort'] == 1);
				$isFillBlank = ($questype && $questype['questsort'] == 0 && $questype['questchoice'] == 5);
			} catch (Exception $e) {
				// 获取题型失败，使用默认解析
			}
		}
		
		// 记录解析开始
		$parseLogData = array(
			'parse_id' => $parseId,
			'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
			'content_length' => strlen($aiContent),
			'content_lines' => substr_count($aiContent, "\n") + 1,
			'content_words' => str_word_count(strip_tags($aiContent)),
			'content_encoding' => mb_detect_encoding($aiContent),
			'question_type' => $questionType,
			'question_type_info' => $questype,
			'is_subjective' => $isSubjective,
			'is_fill_blank' => $isFillBlank,
			'content_preview' => substr($aiContent, 0, 300),
			'parse_method' => 'json'
		);
		
		$this->logDeepSeekInfo('parse_start', '开始解析AI返回的JSON内容', $parseLogData);
		
		// 记录完整的原始内容用于问题分析
		$this->logDeepSeekInfo('parse_original_content', 'JSON解析原始内容完整记录', array(
			'parse_id' => $parseId,
			'user' => $parseLogData['user'],
			'question_type' => $questionType,
			'is_subjective' => $isSubjective,
			'is_fill_blank' => $isFillBlank,
			'content_metadata' => array(
				'length' => strlen($aiContent),
				'lines' => substr_count($aiContent, "\n") + 1,
				'encoding' => mb_detect_encoding($aiContent),
				'size_kb' => round(strlen($aiContent) / 1024, 2)
			),
			'full_original_content' => $aiContent,
			'json_indicators' => array(
				'starts_with_brace' => (trim($aiContent)[0] === '{'),
				'starts_with_bracket' => (trim($aiContent)[0] === '['),
				'contains_question_field' => (strpos($aiContent, '"question"') !== false),
				'contains_options_field' => (strpos($aiContent, '"options"') !== false),
				'contains_answer_field' => (strpos($aiContent, '"answer"') !== false),
				'contains_explanation_field' => (strpos($aiContent, '"explanation"') !== false),
				'brace_count' => substr_count($aiContent, '{'),
				'bracket_count' => substr_count($aiContent, '[')
			)
		));
		
		// 清理内容：移除可能的代码块标记
		$cleanContent = trim($aiContent);
		$cleanContent = preg_replace('/^```json\s*/i', '', $cleanContent);
		$cleanContent = preg_replace('/\s*```$/', '', $cleanContent);
		$cleanContent = preg_replace('/^```\s*/', '', $cleanContent);
		$cleanContent = trim($cleanContent);
		
		$this->logDeepSeekInfo('json_content_cleaned', 'JSON内容清理完成', array(
			'parse_id' => $parseId,
			'user' => $parseLogData['user'],
			'original_length' => strlen($aiContent),
			'cleaned_length' => strlen($cleanContent),
			'removed_markers' => strlen($aiContent) - strlen($cleanContent),
			'cleaned_preview' => substr($cleanContent, 0, 200)
		));
		
		// 尝试解析JSON
		$jsonData = json_decode($cleanContent, true);
		$jsonError = json_last_error();
		$jsonErrorMsg = json_last_error_msg();
		
		if ($jsonError !== JSON_ERROR_NONE) {
			$this->logDeepSeekError('json_parse_error', 'JSON解析失败', array(
				'parse_id' => $parseId,
				'user' => $parseLogData['user'],
				'json_error_code' => $jsonError,
				'json_error_message' => $jsonErrorMsg,
				'content_sample' => substr($cleanContent, 0, 500),
				'content_length' => strlen($cleanContent)
			));
			
			// JSON解析失败，返回空结果
			return $result;
		}
		
		$this->logDeepSeekInfo('json_parse_success', 'JSON解析成功', array(
			'parse_id' => $parseId,
			'user' => $parseLogData['user'],
			'parsed_data_type' => gettype($jsonData),
			'is_array' => is_array($jsonData),
			'data_structure' => is_array($jsonData) ? 
				(isset($jsonData[0]) ? 'indexed_array' : 'associative_array') : 
				'not_array'
		));
		
		// 处理解析结果
		if (is_array($jsonData)) {
			// 如果是数组，取第一个元素（批量生成时）
			if (isset($jsonData[0]) && is_array($jsonData[0])) {
				$questionData = $jsonData[0];
				$this->logDeepSeekInfo('json_array_processing', '处理JSON数组，取第一个元素', array(
					'parse_id' => $parseId,
					'user' => $parseLogData['user'],
					'array_length' => count($jsonData),
					'first_element_keys' => array_keys($questionData)
				));
			} else {
				$questionData = $jsonData;
			}
			
			// 映射JSON字段到我们的格式
			if (isset($questionData['question'])) {
				$result['question'] = trim($questionData['question']);
			}
			
			// 对于主观题和填空题，不处理options字段
			if (!$isSubjective && !$isFillBlank && isset($questionData['options'])) {
				$result['questionselect'] = trim($questionData['options']);
			} else if ($isSubjective || $isFillBlank) {
				// 主观题和填空题无选项
				$result['questionselect'] = '';
			} else if (isset($questionData['options'])) {
				// 客观题但没有检测到主观题或填空题标志，仍然处理options
				$result['questionselect'] = trim($questionData['options']);
			}
			
			if (isset($questionData['answer'])) {
				$result['answer'] = trim($questionData['answer']);
			}
			
			if (isset($questionData['explanation'])) {
				$result['questiondescribe'] = trim($questionData['explanation']);
			}
			
			$this->logDeepSeekInfo('json_field_mapping', 'JSON字段映射完成', array(
				'parse_id' => $parseId,
				'user' => $parseLogData['user'],
				'is_subjective' => $isSubjective,
				'is_fill_blank' => $isFillBlank,
				'mapped_fields' => array(
					'question' => !empty($result['question']),
					'options' => !empty($result['questionselect']),
					'answer' => !empty($result['answer']),
					'explanation' => !empty($result['questiondescribe'])
				),
				'field_lengths' => array(
					'question' => strlen($result['question']),
					'options' => strlen($result['questionselect']),
					'answer' => strlen($result['answer']),
					'explanation' => strlen($result['questiondescribe'])
				),
				'source_json_keys' => array_keys($questionData),
				'question_type_detection' => array(
					'provided_type_id' => $questionType,
					'questype_info' => $questype,
					'detected_as_subjective' => $isSubjective,
					'is_fill_blank' => $isFillBlank,
					'has_options_in_json' => isset($questionData['options']),
					'final_has_options' => !empty($result['questionselect'])
				)
			));
		}
		
		// 清理题干中的题号标识
		if (!empty($result['question'])) {
			$originalQuestion = $result['question'];
			
			// 定义各种题号模式
			$questionNumberPatterns = array(
				'/^【第\s*\d+\s*题[：:】]/u',          // 【第1题：】【第1题】
				'/^第\s*\d+\s*题[：:]/u',            // 第1题：
				'/^第\s*\d+\s*道[：:]/u',            // 第1道：
				'/^\d+\s*[、．.][：:]?\s*/u',        // 1. 1、1：
				'/^题目\s*\d+\s*[：:]/u',            // 题目1：
				'/^试题\s*\d+\s*[：:]/u',            // 试题1：
				'/^\d+\s*[\)）][：:]?\s*/u',         // 1) 1）
				'/^Question\s*\d+\s*[：:]/iu',       // Question1:
				'/^Q\d+\s*[：:]/iu',                // Q1:
				'/^【题目\s*\d*】/u',                // 【题目1】【题目】
				'/^【题干】/u',                      // 【题干】
				'/^【问题】/u'                       // 【问题】
			);
			
			// 尝试匹配并清理
			$cleanedQuestion = $result['question'];
			$matchedPattern = '';
			
			foreach ($questionNumberPatterns as $pattern) {
				if (preg_match($pattern, $cleanedQuestion, $matches)) {
					$cleanedQuestion = preg_replace($pattern, '', $cleanedQuestion);
					$cleanedQuestion = trim($cleanedQuestion);
					$matchedPattern = $pattern;
					break;
				}
			}
			
			// 如果清理后题干为空或太短，保留原始题干
			if (empty($cleanedQuestion) || mb_strlen($cleanedQuestion) < 5) {
				$cleanedQuestion = $originalQuestion;
				$cleaningSuccess = false;
			} else {
				$cleaningSuccess = true;
				$result['question'] = $cleanedQuestion;
			}
			
			// 记录清理过程
			if ($matchedPattern) {
				$this->logDeepSeekInfo('question_number_cleaning', '题干题号清理', array(
					'parse_id' => $parseId,
					'user' => $parseLogData['user'],
					'cleaning_success' => $cleaningSuccess,
					'matched_pattern' => $matchedPattern,
					'original_question' => substr($originalQuestion, 0, 100),
					'cleaned_question' => substr($cleanedQuestion, 0, 100),
					'original_length' => mb_strlen($originalQuestion),
					'cleaned_length' => mb_strlen($cleanedQuestion),
					'removed_chars' => mb_strlen($originalQuestion) - mb_strlen($cleanedQuestion)
				));
			}
		}
		
		// 记录解析结果
		$parseEndTime = microtime(true);
		$parseDuration = round(($parseEndTime - $parseStartTime) * 1000, 2); // 毫秒
		
		$parseResult = array(
			'parse_duration_ms' => $parseDuration,
			'parse_method' => 'json',
			'question_type_id' => $questionType,
			'is_subjective' => $isSubjective,
			'is_fill_blank' => $isFillBlank,
			'json_parse_success' => ($jsonError === JSON_ERROR_NONE),
			'has_question' => !empty($result['question']),
			'has_options' => !empty($result['questionselect']),
			'has_answer' => !empty($result['answer']),
			'has_explain' => !empty($result['questiondescribe']),
			'question_length' => strlen($result['question']),
			'options_length' => strlen($result['questionselect']),
			'answer_length' => strlen($result['answer']),
			'explain_length' => strlen($result['questiondescribe']),
			'total_content_length' => strlen($result['question']) + strlen($result['questionselect']) + strlen($result['answer']) + strlen($result['questiondescribe']),
			'completeness_score' => $this->calculateCompletenessScore($result, $isSubjective, $isFillBlank)
		);
		
		$this->logDeepSeekInfo('parse_complete', 'JSON解析完成', array(
			'parse_id' => $parseId,
			'user' => $parseLogData['user'],
			'parse_results' => $parseResult
		));
		
		return $result;
	}
	
	/**
	 * 计算题目完整性得分
	 * @param array $result 解析结果
	 * @param bool $isSubjective 是否为主观题
	 * @param bool $isFillBlank 是否为填空题
	 * @return float 完整性得分（0-1）
	 */
	private function calculateCompletenessScore($result, $isSubjective = false, $isFillBlank = false)
	{
		$score = 0;
		// 主观题和填空题都不需要options字段，所以总字段数是3
		$totalFields = ($isSubjective || $isFillBlank) ? 3 : 4;
		
		// 题干是必须的
		if (!empty($result['question'])) {
			$score += 1;
		}
		
		// 选项（仅需要选项的客观题需要）
		if (!$isSubjective && !$isFillBlank && !empty($result['questionselect'])) {
			$score += 1;
		} else if ($isSubjective || $isFillBlank) {
			// 主观题和填空题跳过选项检查，直接加分
			$score += 1;
		}
		
		// 答案
		if (!empty($result['answer'])) {
			$score += 1;
		}
		
		// 解析
		if (!empty($result['questiondescribe'])) {
			$score += 1;
		}
		
		return round($score / $totalFields, 3);
	}

	/**
	 * 解析AI批量返回的JSON格式试题内容
	 * @param string $aiContent AI生成的JSON数组内容
	 * @param string $questionType 题型ID
	 * @param int $expectedCount 期望的题目数量
	 * @return array 解析后的试题数据数组
	 */
	private function parseBatchAIResponse($aiContent, $questionType, $expectedCount)
	{
		$batchParseId = uniqid('batch_parse_', true);
		$batchStartTime = microtime(true);
		$questions = array();
		
		// 获取题型信息用于确定解析策略
		$questype = null;
		$isSubjective = false;
		$isFillBlank = false;
		if ($questionType) {
			try {
				$questype = M("basic","exam")->getQuestypeById($questionType);
				$isSubjective = ($questype && $questype['questsort'] == 1);
				$isFillBlank = ($questype && $questype['questsort'] == 0 && $questype['questchoice'] == 5);
			} catch (Exception $e) {
				// 获取题型失败，使用默认解析
			}
		}
		
		$batchLogData = array(
			'batch_parse_id' => $batchParseId,
			'user' => $this->user["sessionusername"] ?? $this->user["username"] ?? 'unknown',
			'content_length' => strlen($aiContent),
			'content_lines' => substr_count($aiContent, "\n") + 1,
			'expected_count' => $expectedCount,
			'question_type' => $questionType,
			'question_type_info' => $questype,
			'is_subjective' => $isSubjective,
			'is_fill_blank' => $isFillBlank,
			'content_preview' => substr($aiContent, 0, 400),
			'parse_method' => 'json_batch'
		);
		
		$this->logDeepSeekInfo('batch_parse_start', '开始批量解析AI返回的JSON内容', $batchLogData);
		
		// 记录完整的批量内容用于问题分析
		$this->logDeepSeekInfo('batch_original_content', 'JSON批量解析原始内容完整记录', array(
			'batch_parse_id' => $batchParseId,
			'user' => $batchLogData['user'],
			'question_type' => $questionType,
			'is_subjective' => $isSubjective,
			'is_fill_blank' => $isFillBlank,
			'expected_count' => $expectedCount,
			'content_metadata' => array(
				'length' => strlen($aiContent),
				'lines' => substr_count($aiContent, "\n") + 1,
				'encoding' => mb_detect_encoding($aiContent),
				'size_kb' => round(strlen($aiContent) / 1024, 2),
				'avg_chars_per_expected_question' => round(strlen($aiContent) / max(1, $expectedCount), 2)
			),
			'full_batch_content' => $aiContent,
			'json_structure_indicators' => array(
				'starts_with_array' => (trim($aiContent)[0] === '['),
				'starts_with_object' => (trim($aiContent)[0] === '{'),
				'array_bracket_count' => substr_count($aiContent, '[') + substr_count($aiContent, ']'),
				'object_brace_count' => substr_count($aiContent, '{') + substr_count($aiContent, '}'),
				'question_fields' => substr_count($aiContent, '"question"'),
				'options_fields' => substr_count($aiContent, '"options"'),
				'answer_fields' => substr_count($aiContent, '"answer"'),
				'explanation_fields' => substr_count($aiContent, '"explanation"')
			)
		));
		
		// 清理内容：移除可能的代码块标记
		$cleanContent = trim($aiContent);
		$cleanContent = preg_replace('/^```json\s*/i', '', $cleanContent);
		$cleanContent = preg_replace('/\s*```$/', '', $cleanContent);
		$cleanContent = preg_replace('/^```\s*/', '', $cleanContent);
		$cleanContent = trim($cleanContent);
		
		$this->logDeepSeekInfo('batch_json_content_cleaned', 'JSON批量内容清理完成', array(
			'batch_parse_id' => $batchParseId,
			'user' => $batchLogData['user'],
			'original_length' => strlen($aiContent),
			'cleaned_length' => strlen($cleanContent),
			'removed_markers' => strlen($aiContent) - strlen($cleanContent),
			'cleaned_preview' => substr($cleanContent, 0, 300)
		));
		
		// 尝试解析JSON
		$jsonData = json_decode($cleanContent, true);
		$jsonError = json_last_error();
		$jsonErrorMsg = json_last_error_msg();
		
		if ($jsonError !== JSON_ERROR_NONE) {
			$this->logDeepSeekError('batch_json_parse_error', 'JSON批量解析失败', array(
				'batch_parse_id' => $batchParseId,
				'user' => $batchLogData['user'],
				'json_error_code' => $jsonError,
				'json_error_message' => $jsonErrorMsg,
				'content_sample' => substr($cleanContent, 0, 1000),
				'content_length' => strlen($cleanContent)
			));
			
			// JSON解析失败，返回空数组
			return array();
		}
		
		$this->logDeepSeekInfo('batch_json_parse_success', 'JSON批量解析成功', array(
			'batch_parse_id' => $batchParseId,
			'user' => $batchLogData['user'],
			'parsed_data_type' => gettype($jsonData),
			'is_array' => is_array($jsonData),
			'array_length' => is_array($jsonData) ? count($jsonData) : 0,
			'first_element_type' => is_array($jsonData) && count($jsonData) > 0 ? gettype($jsonData[0]) : 'none'
		));
		
		// 处理解析结果
		if (is_array($jsonData)) {
			// 如果jsonData本身就是一个对象（单个题目），转换为数组
			if (isset($jsonData['question']) && !isset($jsonData[0])) {
				$jsonData = array($jsonData);
				$this->logDeepSeekInfo('single_object_to_array', '单个JSON对象转换为数组', array(
					'batch_parse_id' => $batchParseId,
					'user' => $batchLogData['user'],
					'converted_to_array' => true
				));
			}
			
			// 遍历处理每个题目
			foreach ($jsonData as $index => $questionData) {
				if (!is_array($questionData)) {
					$this->logDeepSeekInfo('invalid_question_data', "题目#{$index}数据格式无效", array(
						'batch_parse_id' => $batchParseId,
						'user' => $batchLogData['user'],
						'question_index' => $index,
						'data_type' => gettype($questionData),
						'data_preview' => substr(json_encode($questionData), 0, 100)
					));
					continue;
				}
				
				$this->logDeepSeekInfo('processing_question_item', "处理题目#{$index}", array(
					'batch_parse_id' => $batchParseId,
					'user' => $batchLogData['user'],
					'question_index' => $index,
					'available_fields' => array_keys($questionData),
					'has_required_fields' => array(
						'question' => isset($questionData['question']),
						'options' => isset($questionData['options']),
						'answer' => isset($questionData['answer']),
						'explanation' => isset($questionData['explanation'])
					),
					'is_subjective' => $isSubjective,
					'is_fill_blank' => $isFillBlank
				));
				
				// 映射JSON字段到我们的格式
				$result = array(
					'question' => '',
					'questionselect' => '',
					'questiondescribe' => '',
					'answer' => ''
				);
				
				if (isset($questionData['question'])) {
					$result['question'] = trim($questionData['question']);
				}
				
				// 根据题型处理选项字段
				if (!$isSubjective && isset($questionData['options'])) {
					$result['questionselect'] = trim($questionData['options']);
				} else if ($isSubjective) {
					// 主观题无选项
					$result['questionselect'] = '';
				} else if (isset($questionData['options'])) {
					// 客观题但没有检测到主观题标志，仍然处理options
					$result['questionselect'] = trim($questionData['options']);
				}
				
				if (isset($questionData['answer'])) {
					$result['answer'] = trim($questionData['answer']);
				}
				
				if (isset($questionData['explanation'])) {
					$result['questiondescribe'] = trim($questionData['explanation']);
				}
				
				// 清理题干中的题号标识（批量解析）
				if (!empty($result['question'])) {
					$originalQuestion = $result['question'];
					
					// 定义各种题号模式
					$questionNumberPatterns = array(
						'/^【第\s*\d+\s*题[：:】]/u',          // 【第1题：】【第1题】
						'/^第\s*\d+\s*题[：:]/u',            // 第1题：
						'/^第\s*\d+\s*道[：:]/u',            // 第1道：
						'/^\d+\s*[、．.][：:]?\s*/u',        // 1. 1、1：
						'/^题目\s*\d+\s*[：:]/u',            // 题目1：
						'/^试题\s*\d+\s*[：:]/u',            // 试题1：
						'/^\d+\s*[\)）][：:]?\s*/u',         // 1) 1）
						'/^Question\s*\d+\s*[：:]/iu',       // Question1:
						'/^Q\d+\s*[：:]/iu',                // Q1:
						'/^【题目\s*\d*】/u',                // 【题目1】【题目】
						'/^【题干】/u',                      // 【题干】
						'/^【问题】/u'                       // 【问题】
					);
					
					// 尝试匹配并清理
					$cleanedQuestion = $result['question'];
					$matchedPattern = '';
					
					foreach ($questionNumberPatterns as $pattern) {
						if (preg_match($pattern, $cleanedQuestion, $matches)) {
							$cleanedQuestion = preg_replace($pattern, '', $cleanedQuestion);
							$cleanedQuestion = trim($cleanedQuestion);
							$matchedPattern = $pattern;
							break;
						}
					}
					
					// 如果清理后题干为空或太短，保留原始题干
					if (empty($cleanedQuestion) || mb_strlen($cleanedQuestion) < 5) {
						$cleanedQuestion = $originalQuestion;
						$cleaningSuccess = false;
					} else {
						$cleaningSuccess = true;
						$result['question'] = $cleanedQuestion;
					}
					
					// 记录清理过程
					if ($matchedPattern) {
						$this->logDeepSeekInfo('batch_question_number_cleaning', '批量解析题干题号清理', array(
							'batch_parse_id' => $batchParseId,
							'user' => $batchLogData['user'],
							'question_index' => $index,
							'cleaning_success' => $cleaningSuccess,
							'matched_pattern' => $matchedPattern,
							'original_question' => substr($originalQuestion, 0, 100),
							'cleaned_question' => substr($cleanedQuestion, 0, 100),
							'original_length' => mb_strlen($originalQuestion),
							'cleaned_length' => mb_strlen($cleanedQuestion),
							'removed_chars' => mb_strlen($originalQuestion) - mb_strlen($cleanedQuestion)
						));
					}
				}
				
				// 检查题目完整性（使用智能评分）
				$completeness = $this->calculateCompletenessScore($result, $isSubjective, $isFillBlank);
				
				if (!empty($result['question'])) {
					$questions[] = $result;
					
					$this->logDeepSeekInfo('question_processed_successfully', "题目#{$index}处理成功", array(
						'batch_parse_id' => $batchParseId,
						'user' => $batchLogData['user'],
						'question_index' => $index,
						'is_subjective' => $isSubjective,
						'is_fill_blank' => $isFillBlank,
						'completeness_score' => $completeness,
						'field_lengths' => array(
							'question' => strlen($result['question']),
							'options' => strlen($result['questionselect']),
							'answer' => strlen($result['answer']),
							'explanation' => strlen($result['questiondescribe'])
						),
						'question_type_detection' => array(
							'provided_type_id' => $questionType,
							'questype_info' => $questype,
							'detected_as_subjective' => $isSubjective,
							'is_fill_blank' => $isFillBlank,
							'has_options_in_json' => isset($questionData['options']),
							'final_has_options' => !empty($result['questionselect'])
						)
					));
				} else {
					$this->logDeepSeekInfo('question_processing_failed', "题目#{$index}处理失败：题干为空", array(
						'batch_parse_id' => $batchParseId,
						'user' => $batchLogData['user'],
						'question_index' => $index,
						'is_subjective' => $isSubjective,
						'is_fill_blank' => $isFillBlank,
						'completeness_score' => $completeness,
						'source_data' => $questionData
					));
				}
				
				// 限制数量
				if (count($questions) >= $expectedCount) {
					$this->logDeepSeekInfo('batch_limit_reached', '已达到期望题目数量，停止处理', array(
						'batch_parse_id' => $batchParseId,
						'user' => $batchLogData['user'],
						'questions_processed' => count($questions),
						'expected_count' => $expectedCount,
						'remaining_items' => count($jsonData) - $index - 1
					));
					break;
				}
			}
		}
		
		// 记录批量解析总结
		$batchEndTime = microtime(true);
		$batchDuration = round(($batchEndTime - $batchStartTime) * 1000, 2);
		
		$batchSummary = array(
			'batch_duration_ms' => $batchDuration,
			'parse_method' => 'json_batch',
			'question_type_id' => $questionType,
			'is_subjective' => $isSubjective,
			'is_fill_blank' => $isFillBlank,
			'json_parse_success' => ($jsonError === JSON_ERROR_NONE),
			'expected_count' => $expectedCount,
			'actual_count' => count($questions),
			'success_rate' => round(count($questions) / max(1, $expectedCount), 3),
			'json_items_found' => is_array($jsonData) ? count($jsonData) : 0,
			'processing_efficiency' => is_array($jsonData) && count($jsonData) > 0 ? 
				round(count($questions) / count($jsonData), 3) : 0,
			'content_efficiency' => round(strlen($aiContent) / max(1, count($questions)), 2), // 字符数/题目数
			'questions_summary' => array()
		);
		
		// 为每个解析成功的题目生成摘要
		foreach ($questions as $index => $question) {
			$batchSummary['questions_summary'][] = array(
				'index' => $index,
				'question_length' => strlen($question['question']),
				'has_options' => !empty($question['questionselect']),
				'has_answer' => !empty($question['answer']),
				'has_explain' => !empty($question['questiondescribe']),
				'completeness_score' => $this->calculateCompletenessScore($question, $isSubjective, $isFillBlank)
			);
		}
		
		$this->logDeepSeekInfo('batch_parse_complete', 'JSON批量解析完成', array(
			'batch_parse_id' => $batchParseId,
			'user' => $batchLogData['user'],
			'batch_summary' => $batchSummary
		));
		
		return $questions;
	}

	/**
	 * 记录DeepSeek API错误日志
	 */
	private function logDeepSeekError($action, $message, $data = array()) {
		$this->writeDeepSeekLog('ERROR', $action, $message, $data);
	}
	
	/**
	 * 记录DeepSeek API信息日志
	 */
	private function logDeepSeekInfo($action, $message, $data = array()) {
		$this->writeDeepSeekLog('INFO', $action, $message, $data);
	}
	
	/**
	 * 记录DeepSeek API配置错误日志（兼容旧格式）
	 */
	private function logConfigError($action, $message, $errors, $data = array()) {
		$logFile = PEPATH . '/data/deepseek_api.log';
		$logDir = dirname($logFile);
		
		// 确保日志目录存在
		if (!is_dir($logDir)) {
			@mkdir($logDir, 0755, true);
		}
		
		// 兼容旧的配置错误日志格式
		$logEntry = array(
			'timestamp' => date('Y-m-d H:i:s'),
			'action' => $action,
			'message' => $message,
			'data' => $errors,
			'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
		);
		
		$logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";
		
		// 写入日志文件
		@file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
		
		// 同时写入标准格式的错误日志
		$errorMessage = $message . ': ' . implode(', ', $errors);
		$this->writeDeepSeekLog('ERROR', $action, $errorMessage, $data);
	}
	
	/**
	 * 写入DeepSeek API日志
	 */
	private function writeDeepSeekLog($level, $action, $message, $data = array()) {
		$logFile = PEPATH . '/data/deepseek_api.log';
		$logDir = dirname($logFile);
		
		// 确保日志目录存在
		if (!is_dir($logDir)) {
			@mkdir($logDir, 0755, true);
		}
		
		$logEntry = array(
			'timestamp' => date('Y-m-d H:i:s'),
			'level' => $level,
			'action' => $action,
			'message' => $message,
			'data' => $data,
			'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
			'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
		);
		
		$logLine = json_encode($logEntry, JSON_UNESCAPED_UNICODE) . "\n";
		
		// 写入日志文件
		@file_put_contents($logFile, $logLine, FILE_APPEND | LOCK_EX);
	}
}


?>
