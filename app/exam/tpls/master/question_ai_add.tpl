{x2;if:!$userhash}
{x2;include:header}
<body>
{x2;include:nav}
<div class="container-fluid">
	<div class="row-fluid">
		<div class="main">
			<div class="col-xs-2 leftmenu">
				{x2;include:menu}
			</div>
			<div id="datacontent">
{x2;endif}
				<div class="box itembox" style="margin-bottom:0px;border-bottom:1px solid #CCCCCC;">
					<div class="col-xs-12">
						<ol class="breadcrumb">
							<li><a href="index.php?{x2;$_app}-master">{x2;$apps[$_app]['appname']}</a></li>
							<li><a href="index.php?{x2;$_app}-master-questions&page={x2;$page}{x2;$u}">普通试题管理</a></li>
							<li class="active">博众AI添加试题</li>
						</ol>
					</div>
				</div>
				<div class="box itembox" style="padding-top:10px;margin-bottom:0px;">
					<h4 class="title" style="padding:10px;">
						<i class="glyphicon glyphicon-flash text-success"></i> 博众AI批量添加试题
						<a class="btn btn-primary pull-right" href="index.php?{x2;$_app}-master-questions&page={x2;$page}{x2;$u}">普通试题管理</a>
					</h4>
					<div class="alert alert-info">
						<i class="glyphicon glyphicon-info-sign"></i> 
						<strong>功能说明：</strong>基于人工智能技术，通过AI提示词批量快速生成高质量试题。请先选择知识点、题型和生成数量，然后提供详细的AI提示词，系统将自动生成符合要求的试题内容供您预览和选择。
					</div>
					
					<!-- 第一步：基础配置 -->
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="glyphicon glyphicon-cog"></i> 第一步：基础配置</h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label class="control-label col-sm-2">知识点：</label>
								<div class="col-sm-10">
									<textarea class="form-control" rows="4" needle="needle" min="1" msg="您最少需要选定一个知识点" name="args[questionknowsid]" id="questionknowsid" readonly></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2"></label>
								<div class="col-sm-10 form-inline">
									<select class="combox form-control" target="isectionselect" refUrl="?exam-master-questions-ajax-getsectionsbysubjectid&subjectid={value}">
										<option value="0">选择科目</option>
										{x2;tree:$subjects,subject,sid}
										<option value="{x2;v:subject['subjectid']}">{x2;v:subject['subject']}</option>
										{x2;endtree}
									</select>
									<select class="combox form-control" id="isectionselect" target="iknowsselect" refUrl="?exam-master-questions-ajax-getknowsbysectionid&sectionid={value}">
										<option value="0">选择章节</option>
									</select>
									<select class="combox form-control" id="iknowsselect">
										<option value="0">选择知识点</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-sm-1">题型：</label>
								<div class="col-sm-1 form-inline">
									<select needle="needle" msg="您必须为要添加的试题选择一种题型" name="args[questiontype]" id="questiontype" class="form-control" onchange="javascript:generateAIPrompt();">
										<option value="">请选择题型</option>
										{x2;tree:$questypes,questype,qid}
										<option rel="{x2;if:v:questype['questsort']}0{x2;else}{x2;v:questype['questchoice']}{x2;endif}" value="{x2;v:questype['questid']}"{x2;if:v:questype['questid'] == $question['questiontype']} selected{x2;endif}>{x2;v:questype['questype']}</option>
										{x2;endtree}
									</select>
								</div>

								<label class="control-label col-sm-1 form-inline">生成数量：</label>
								<div class="col-sm-1 form-inline">
									<select class="form-control" id="generate_count" name="generate_count">
										<option value="5">5道题</option>
										<option value="10" selected>10道题</option>
										<option value="20">20道题</option>
										<option value="50">50道题</option>
									</select>
								</div>

								<label class="control-label col-sm-1 form-inline">难度：</label>
								<div class="col-sm-1 form-inline">
									<select class="form-control" id="questionlevel" name="args[questionlevel]" needle="needle" msg="您必须为要添加的试题设置一个难度">
										<option value="1" selected>易</option>
										<option value="2">中</option>
										<option value="3">难</option>
									</select>
								</div>
							</div>

							<div class="form-group">
								<label class="control-label col-sm-2"></label>
								<div class="col-sm-9">
									<input type="button" class="btn btn-primary" value="选定" onclick="javascript:setKnowsList('questionknowsid','iknowsselect','+');generateAIPrompt();"/>
									<input type="button" class="btn btn-danger" value="清除" onclick="javascript:setKnowsList('questionknowsid','iknowsselect','-');clearAIPrompt();"/>
									<input type="button" class="btn btn-warning" value="储存" onclick="javascript:$.cookie('phpems-knowsselector',$('#questionknowsid').val());alert('储存成功');"/>
									<input type="button" class="btn btn-info" value="载入" onclick="javascript:$('#questionknowsid').val($.cookie('phpems-knowsselector'));"/>
								</div>
							</div>

						</div>
					</div>
					
					<!-- 第二步：AI提示词配置 -->
					<div class="panel panel-success">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="glyphicon glyphicon-flash"></i> 第二步：AI提示词配置</h3>
						</div>
						<div class="panel-body">
							<div class="form-group">
								<label class="control-label col-sm-2">AI提示词：</label>
								<div class="col-sm-10">
									<textarea class="form-control" name="args[aiprompt]" id="aiprompt" rows="8" needle="needle" msg="请输入AI提示词" placeholder="请在此输入详细的AI提示词，描述您希望生成的试题要求..."></textarea>
									<div class="help-block">
										<strong>提示词示例：</strong><br>
										• 请生成关于[知识点]的[题型]题目，难度为[难度级别]<br>
										• 题目应当考察学生对[具体概念]的理解和应用能力<br>
										• 如果是选择题，请提供4个选项，其中只有一个正确答案<br>
										• 请同时提供详细的答案解析
									</div>
								</div>
							</div>
							<div class="form-group">
								<label class="control-label col-sm-2"></label>
								<div class="col-sm-10">
									<div class="btn-group">
										<button type="button" class="btn btn-info" onclick="javascript:generateAIPrompt();">
											<i class="glyphicon glyphicon-magic"></i> 自动生成提示词
										</button>
										<button type="button" class="btn btn-warning" onclick="javascript:clearAIPrompt();">
											<i class="glyphicon glyphicon-trash"></i> 清空提示词
										</button>
										<button type="button" id="btn_generate_ai" class="btn btn-success btn-lg" onclick="javascript:generateBatchAIQuestions();">
											<i class="glyphicon glyphicon-flash"></i> AI批量生成试题
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<!-- 第三步：生成结果预览 -->
					<div class="panel panel-warning" id="preview_panel" style="display:none;">
						<div class="panel-heading">
							<h3 class="panel-title"><i class="glyphicon glyphicon-eye-open"></i> 第三步：生成结果预览与选择</h3>
						</div>
						<div class="panel-body">
							<div class="alert alert-warning">
								<i class="glyphicon glyphicon-exclamation-sign"></i>
								<strong>请仔细审核每道试题：</strong>请检查题干、选项、答案和解析的准确性，只选择质量合格的试题进行保存。
							</div>
							<form id="batch_save_form" action="index.php?exam-master-questions-aiaddquestion" method="post">
								<div id="questions_preview_table"></div>
								<div class="form-group" style="margin-top:20px;">
									<button type="button" class="btn btn-success btn-lg" onclick="javascript:saveBatchQuestions();">
										<i class="glyphicon glyphicon-ok"></i> 保存选中的试题
									</button>
									<button type="button" class="btn btn-default" onclick="javascript:hidePreview();">
										<i class="glyphicon glyphicon-arrow-left"></i> 重新生成
									</button>
									<span class="text-muted" style="margin-left:15px;">
										<i class="glyphicon glyphicon-info-sign"></i> 请至少选择一道试题进行保存
									</span>
								</div>
								<input type="hidden" name="page" value="{x2;$page}"/>
								<input type="hidden" name="batchsaveaiquestions" value="1"/>
								{x2;tree:$search,arg,aid}
								<input type="hidden" name="search[{x2;v:key}]" value="{x2;v:arg}"/>
								{x2;endtree}
							</form>
						</div>
					</div>
				</div>
			</div>
{x2;if:!$userhash}
		</div>
	</div>
</div>

<!-- AI功能相关的JavaScript代码 -->
<script type="text/javascript">
// 全局变量存储生成的试题数据
var generatedQuestionsData = [];

// 防抖变量：记录上次生成时间
var lastGenerateTime = 0;
var generateDebounceTime = 10000; // 10秒防抖时间

// 自动生成AI提示词
function generateAIPrompt() {
	var knowsText = $('#questionknowsid').val();
	var questionType = $('#questiontype option:selected').text();
	var generateCount = $('#generate_count').val();
	
	if (!knowsText || !questionType || questionType === '请选择题型') {
		alert('请先选择知识点和题型');
		return;
	}
	
	var difficulty = $('#questionlevel option:selected').text();
	var prompt = '请基于"' + knowsText + '"这个知识点，生成' + generateCount + '道' + questionType + '，难度等级为：' + difficulty + '。\n\n';
	
	if (questionType.indexOf('单选') >= 0) {
		prompt += '要求：\n1. 每道题的题干要清晰明确，考察对核心概念的理解\n2. 每道题提供4个选项（A、B、C、D），只有一个正确答案\n3. 错误选项要有一定迷惑性，但不能过于明显\n4. 每道题提供详细的解析说明\n\n';
	} else if (questionType.indexOf('多选') >= 0) {
		prompt += '要求：\n1. 每道题的题干要全面考察知识点的多个方面\n2. 每道题提供4-6个选项，其中2-3个为正确答案\n3. 各选项要相互独立，避免重复\n4. 每道题提供详细的解析说明\n\n';
	} else if (questionType.indexOf('判断') >= 0) {
		prompt += '要求：\n1. 每道题的题干要针对关键概念或易混淆点\n2. 只需提供"对"或"错"的答案\n3. 每道题提供详细的解析说明判断理由\n\n';
	} else if (questionType.indexOf('填空') >= 0) {
		prompt += '要求：\n1. 每道题设置1-3个空格，用()表示\n2. 答案要具体明确，避免模糊性\n3. 题干要给出足够的上下文信息\n4. 每道题提供详细的解析说明\n\n';
	} else if (questionType.indexOf('问答') >= 0 || questionType.indexOf('简答') >= 0) {
		prompt += '要求：\n1. 每道题的问题要具有一定的综合性和深度\n2. 答案要分点作答，条理清晰\n3. 涵盖知识点的主要内容\n4. 每道题提供详细的参考答案和评分要点\n\n';
	}
	
	prompt += '请为每道题编号（第1题、第2题...），并按照以下格式输出：\n第N题：\n【题干】\n【选项】（如果有）\n【正确答案】\n【解析】\n\n请确保每道题之间有明确的分割标识。';
	
	$('#aiprompt').val(prompt);
}

// 清空AI提示词
function clearAIPrompt() {
	$('#aiprompt').val('');
}

// AI批量生成试题（带防抖功能）
function generateBatchAIQuestions() {
	// 防抖检查：判断是否在冷却时间内
	var currentTime = new Date().getTime();
	var timeSinceLastGenerate = currentTime - lastGenerateTime;
	
	if (timeSinceLastGenerate < generateDebounceTime) {
		var remainingTime = Math.ceil((generateDebounceTime - timeSinceLastGenerate) / 1000);
		alert('请等待 ' + remainingTime + ' 秒后再次生成，避免频繁请求大模型服务');
		return;
	}
	
	var aiprompt = $('#aiprompt').val();
	var generateCount = $('#generate_count').val();
	var questionType = $('#questiontype').val();
	var knowsText = $('#questionknowsid').val();
	
	if (!aiprompt) {
		alert('请先输入AI提示词');
		return;
	}
	
	if (!questionType) {
		alert('请先选择题型');
		return;
	}
	
	if (!knowsText) {
		alert('请先选择知识点');
		return;
	}
	
	// 更新最后生成时间
	lastGenerateTime = currentTime;
	
	// 显示加载状态（使用固定ID，避免 contains 选择器失效）
	var $generateBtn = $('#btn_generate_ai');
	if ($generateBtn.length === 0) {
		$generateBtn = $('.btn-success:contains("AI批量生成试题")');
	}
	var originalText = $generateBtn.html();
	$generateBtn.html('<i class="glyphicon glyphicon-refresh glyphicon-spin"></i> AI正在生成' + generateCount + '道试题，请耐心等待...').prop('disabled', true);
	
	// 隐藏预览面板
	$('#preview_panel').hide();
	
	// 显示进度提示
	showProgressAlert('正在连接AI服务，生成' + generateCount + '道试题中，预计需要1-5分钟，请耐心等待...');
	
	// 调用AI接口生成试题
	$.ajax({
		url: 'index.php?exam-master-questions-ajax-generateaiquestion',
		type: 'POST',
		data: {
			aiprompt: aiprompt,
			questiontype: questionType,
			knowstext: knowsText,
			generate_count: generateCount
		},
		dataType: 'json',
		timeout: 600000, // 设置前端AJAX超时时间为10分钟
		success: function(response) {
			hideProgressAlert();
			if (response.statusCode === 200) {
				// 保存生成的试题数据
				generatedQuestionsData = response.data.questions;
				
				// 显示预览表格
				showPreviewTable(response.data);
				
				alert('AI批量生成成功！生成了 ' + response.data.success_count + ' 道试题，请仔细审核后选择要保存的试题。');
			} else {
				alert('AI生成失败：' + (response.message || '未知错误'));
			}
		},
		error: function(xhr, status, error) {
			hideProgressAlert();
			if (status === 'timeout') {
				alert('AI生成超时，请稍后重试。如果问题持续存在，请联系技术支持。');
			} else {
				alert('AI接口调用失败，请检查网络连接或稍后重试');
			}
		},
		complete: function() {
			// 恢复按钮状态
			$generateBtn.html(originalText).prop('disabled', false);
			hideProgressAlert();
		}
	});
}

// 显示进度提示框
function showProgressAlert(message) {
	// 移除之前的进度提示
	$('#progress_alert').remove();
	
	var alertHtml = '<div id="progress_alert" class="alert alert-info" style="position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);z-index:9999;width:400px;text-align:center;">';
	alertHtml += '<i class="glyphicon glyphicon-refresh glyphicon-spin"></i> ';
	alertHtml += '<strong>' + message + '</strong>';
	alertHtml += '<div style="margin-top:10px;"><small>请勿关闭页面或刷新浏览器</small></div>';
	alertHtml += '</div>';
	
	$('body').append(alertHtml);
}

// 隐藏进度提示框
function hideProgressAlert() {
	$('#progress_alert').remove();
}

// 显示预览表格
function showPreviewTable(data) {
	var html = '<div class="table-responsive">';
	var hiddenHtml = '';
	html += '<table class="table table-bordered table-hover">';
	html += '<thead>';
	html += '<tr class="info">';
	html += '<th width="50"><input type="checkbox" id="select_all" onchange="toggleSelectAll()"> 全选</th>';
	html += '<th width="60">序号</th>';
	html += '<th>题干</th>';
	html += '<th width="200">选项</th>';
	html += '<th width="100">正确答案</th>';
	html += '<th width="150">解析</th>';
	html += '<th width="80">操作</th>';
	html += '</tr>';
	html += '</thead>';
	html += '<tbody>';
	
	for (var i = 0; i < data.questions.length; i++) {
		var question = data.questions[i];
		var questionNum = i + 1;
		
		html += '<tr>';
		html += '<td><input type="checkbox" name="selected_questions[' + i + ']" value="1" checked></td>';
		html += '<td>' + questionNum + '</td>';
		html += '<td class="text-left">';
		html += '<div style="max-height:150px;overflow-y:auto;">' + htmlEncode(question.question) + '</div>';
		html += '</td>';
		html += '<td class="text-left">';
		if (question.questionselect) {
			html += '<div style="max-height:120px;overflow-y:auto;font-size:12px;">' + htmlEncode(question.questionselect) + '</div>';
		} else {
			html += '<span class="text-muted">无选项</span>';
		}
		html += '</td>';
		html += '<td class="text-center">';
		html += '<strong class="text-primary">' + htmlEncode(question.answer) + '</strong>';
		html += '</td>';
		html += '<td class="text-left">';
		html += '<div style="max-height:120px;overflow-y:auto;font-size:12px;">' + htmlEncode(question.questiondescribe) + '</div>';
		html += '</td>';
		html += '<td class="text-center">';
		html += '<button type="button" class="btn btn-sm btn-info" onclick="previewQuestion(' + i + ')">详情</button>';
		html += '</td>';
		html += '</tr>';
		
		// 添加隐藏字段存储试题数据（不要放到 table/tbody 内，避免浏览器自动重排导致提交字段丢失或表格异常）
		var selectNumber = guessSelectNumber(question.questionselect);
		hiddenHtml += '<input type="hidden" name="questions_data[' + i + '][question]" value="' + htmlEncode(question.question) + '">';
		hiddenHtml += '<input type="hidden" name="questions_data[' + i + '][questionselect]" value="' + htmlEncode(question.questionselect) + '">';
		hiddenHtml += '<input type="hidden" name="questions_data[' + i + '][answer]" value="' + htmlEncode(question.answer) + '">';
		hiddenHtml += '<input type="hidden" name="questions_data[' + i + '][questiondescribe]" value="' + htmlEncode(question.questiondescribe) + '">';
		hiddenHtml += '<input type="hidden" name="questions_data[' + i + '][questiontype]" value="' + data.questiontype + '">';
		hiddenHtml += '<input type="hidden" name="questions_data[' + i + '][questionlevel]" value="' + $('#questionlevel').val() + '">';
		hiddenHtml += '<input type="hidden" name="questions_data[' + i + '][questionknowsid]" value="' + data.knowstext + '">';
		hiddenHtml += '<input type="hidden" name="questions_data[' + i + '][questionselectnumber]" value="' + selectNumber + '">';
	}
	
	html += '</tbody>';
	html += '</table>';
	html += '</div>';
	
	$('#questions_preview_table').html(html + hiddenHtml);
	$('#preview_panel').show();
	
	// 滚动到预览区域
	$('html, body').animate({
		scrollTop: $('#preview_panel').offset().top - 50
	}, 500);
}

// 全选/取消全选
function toggleSelectAll() {
	var checked = $('#select_all').prop('checked');
	$('input[name^="selected_questions"]').prop('checked', checked);
}

// 预览单个试题详情
function previewQuestion(index) {
	var question = generatedQuestionsData[index];
	var html = '<div class="modal fade" id="question_detail_modal" tabindex="-1">';
	html += '<div class="modal-dialog modal-lg">';
	html += '<div class="modal-content">';
	html += '<div class="modal-header">';
	html += '<button type="button" class="close" data-dismiss="modal">&times;</button>';
	html += '<h4 class="modal-title">试题详情 - 第' + (index + 1) + '题</h4>';
	html += '</div>';
	html += '<div class="modal-body">';
	html += '<h5><i class="glyphicon glyphicon-question-sign text-primary"></i> 题干：</h5>';
	html += '<div class="well">' + htmlEncode(question.question) + '</div>';
	
	if (question.questionselect) {
		html += '<h5><i class="glyphicon glyphicon-list text-info"></i> 选项：</h5>';
		html += '<div class="well">' + htmlEncode(question.questionselect) + '</div>';
	}
	
	html += '<h5><i class="glyphicon glyphicon-ok text-success"></i> 正确答案：</h5>';
	html += '<div class="well text-primary"><strong>' + htmlEncode(question.answer) + '</strong></div>';
	html += '<h5><i class="glyphicon glyphicon-comment text-warning"></i> 解析：</h5>';
	html += '<div class="well">' + htmlEncode(question.questiondescribe) + '</div>';
	html += '</div>';
	html += '<div class="modal-footer">';
	html += '<button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	html += '</div>';
	
	// 移除之前的模态框
	$('#question_detail_modal').remove();
	$('body').append(html);
	$('#question_detail_modal').modal('show');
}

// 隐藏预览
function hidePreview() {
	$('#preview_panel').hide();
	generatedQuestionsData = [];
}

// 保存批量试题
function saveBatchQuestions() {
	var selectedCount = $('input[name^="selected_questions"]:checked').length;
	
	if (selectedCount === 0) {
		alert('请至少选择一道试题进行保存');
		return;
	}
	
	if (confirm('确定要保存选中的 ' + selectedCount + ' 道试题吗？')) {
		$('#batch_save_form').submit();
	}
}

// 根据选项文本估算选项数量（用于保存到 questionselectnumber）
function guessSelectNumber(questionselect) {
	if (!questionselect) return 0;
	var text = String(questionselect);
	var lines = text.split(/\r?\n/);
	var count = 0;
	for (var i = 0; i < lines.length; i++) {
		var line = lines[i].trim();
		if (!line) continue;
		if (/^[A-F][\.、\)]\s*/.test(line)) count++;
	}
	if (count > 0) return count;
	// 兜底：如果存在 A. / B. 等标记但没有换行
	var matches = text.match(/[A-F][\.、\)]/g);
	return matches ? Math.min(matches.length, 10) : 0;
}

// HTML编码函数
function htmlEncode(str) {
	if (!str) return '';
	return str.replace(/&/g, '&amp;')
			  .replace(/</g, '&lt;')
			  .replace(/>/g, '&gt;')
			  .replace(/"/g, '&quot;')
			  .replace(/'/g, '&#39;');
}
</script>

{x2;include:footer}
</body>
</html>
{x2;endif} 