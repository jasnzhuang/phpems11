{x2;if:!$userhash}
{x2;include:header}
<body>
{x2;include:nav}
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span2">
			{x2;include:menu}
		</div>
		<div class="span10">
{x2;endif}
			<ul class="breadcrumb">
				<li><a href="index.php?exam-master">管理首页</a> <span class="divider">/</span></li>
				<li><a href="index.php?exam-master-questions">试题管理</a> <span class="divider">/</span></li>
				<li class="active">AI日志管理</li>
			</ul>
			<div class="row-fluid">
				<div class="span12">
					<h4 class="title">
						<i class="icon-list-alt"></i> AI日志管理
						<small>（仅限 zhuang 用户使用）</small>
					</h4>
					
					<!-- 搜索筛选表单 -->
					<form method="get" action="index.php" class="form-inline well">
						<input type="hidden" name="exam-master-questions-ailogs" value="1" />
						
						<div class="row-fluid">
							<div class="span12">
								<label class="control-label">日期筛选：</label>
								<input type="date" name="search[date]" value="{x2;$search['date']}" class="input-small" placeholder="选择日期" />
								
								<label class="control-label" style="margin-left: 20px;">用户筛选：</label>
								<select name="search[user]" class="input-small">
									<option value="">全部用户</option>
									{x2;tree:$users,user,uid}
									<option value="{x2;v:user}" {x2;if:v:user == $search['user']}selected{x2;endif}>{x2;v:user}</option>
									{x2;endtree}
								</select>
								
								<label class="control-label" style="margin-left: 20px;">日志级别：</label>
								<select name="search[level]" class="input-small">
									<option value="">全部级别</option>
									<option value="INFO" {x2;if:$search['level'] == 'INFO'}selected{x2;endif}>信息</option>
									<option value="ERROR" {x2;if:$search['level'] == 'ERROR'}selected{x2;endif}>错误</option>
								</select>
								
								<label class="control-label" style="margin-left: 20px;">操作类型：</label>
								<select name="search[action]" class="input-small">
									<option value="">全部操作</option>
									<option value="request_start" {x2;if:$search['action'] == 'request_start'}selected{x2;endif}>请求开始</option>
									<option value="request_success" {x2;if:$search['action'] == 'request_success'}selected{x2;endif}>请求成功</option>
									<option value="config_missing" {x2;if:$search['action'] == 'config_missing'}selected{x2;endif}>配置缺失</option>
									<option value="config_error" {x2;if:$search['action'] == 'config_error'}selected{x2;endif}>配置错误</option>
									<option value="api_key_missing" {x2;if:$search['action'] == 'api_key_missing'}selected{x2;endif}>密钥缺失</option>
									<option value="api_key_invalid" {x2;if:$search['action'] == 'api_key_invalid'}selected{x2;endif}>密钥无效</option>
									<option value="curl_error" {x2;if:$search['action'] == 'curl_error'}selected{x2;endif}>网络错误</option>
									<option value="http_error" {x2;if:$search['action'] == 'http_error'}selected{x2;endif}>HTTP错误</option>
									<option value="json_parse_error" {x2;if:$search['action'] == 'json_parse_error'}selected{x2;endif}>解析错误</option>
									<option value="response_structure_error" {x2;if:$search['action'] == 'response_structure_error'}selected{x2;endif}>响应结构错误</option>
								</select>
								
								<br><br>
								<label class="control-label">关键词搜索：</label>
								<input type="text" name="search[keyword]" value="{x2;$search['keyword']}" class="input-medium" placeholder="输入关键词" />
								
								<button class="btn btn-primary" type="submit">搜索</button>
								<a href="index.php?exam-master-questions-ailogs" class="btn">重置</a>
								{x2;if:$logData['total'] > 0}
								<a href="javascript:void(0);" onclick="confirmClearLog()" class="btn btn-danger" style="margin-left: 20px;">清空日志</a>
								{x2;endif}
							</div>
						</div>
					</form>
					
					<!-- 日志统计信息 -->
					{x2;if:$logData['total'] > 0}
					<div class="alert alert-info">
						<strong>日志统计：</strong>
						共找到 <strong>{x2;$logData['total']}</strong> 条记录，
						文件大小：<strong>{x2;date:$logData['fileSize'],'KB',2}</strong> KB，
						最后更新：<strong>{x2;date:$logData['lastModified'],'Y-m-d H:i:s'}</strong>
					</div>
					{x2;else}
					<div class="alert alert-warning">
						<strong>提示：</strong>暂无AI日志数据
					</div>
					{x2;endif}
					
					<!-- 日志列表表格 -->
					{x2;if:$logData['total'] > 0}
					<table class="table table-striped table-bordered">
						<thead>
							<tr>
								<th width="150">时间</th>
								<th width="60">级别</th>
								<th width="80">用户</th>
								<th width="120">操作类型</th>
								<th>消息</th>
								<th width="80">详细信息</th>
							</tr>
						</thead>
						<tbody>
							{x2;tree:$logData['data'],log,lid}
							<tr class="{x2;if:v:log['level'] == 'ERROR' || v:log['action'] == 'config_error'}error{x2;endif}">
								<td>{x2;v:log['timestamp']}</td>
								<td>
									{x2;if:v:log['level'] == 'ERROR' || v:log['action'] == 'config_error'}
									<span class="label label-important">错误</span>
									{x2;elseif:v:log['level'] == 'INFO'}
									<span class="label label-info">信息</span>
									{x2;else}
									<span class="label label-warning">未知</span>
									{x2;endif}
								</td>
								<td>
									{x2;if:v:log['data']['user']}
									<span class="user-badge">{x2;v:log['data']['user']}</span>
									{x2;else}
									-
									{x2;endif}
								</td>
								<td>
									{x2;if:v:log['action'] == 'request_start'}请求开始
									{x2;elseif:v:log['action'] == 'request_success'}请求成功
									{x2;elseif:v:log['action'] == 'config_missing'}配置缺失
									{x2;elseif:v:log['action'] == 'config_error'}配置错误
									{x2;elseif:v:log['action'] == 'api_key_missing'}密钥缺失
									{x2;elseif:v:log['action'] == 'api_key_invalid'}密钥无效
									{x2;elseif:v:log['action'] == 'curl_error'}网络错误
									{x2;elseif:v:log['action'] == 'http_error'}HTTP错误
									{x2;elseif:v:log['action'] == 'json_parse_error'}解析错误
									{x2;elseif:v:log['action'] == 'response_structure_error'}响应结构错误
									{x2;else}{x2;v:log['action']}
									{x2;endif}
								</td>
								<td class="log-message">
									{x2;v:log['message']}
									{x2;if:is_array(v:log['data']) && !v:log['data']['user']}
									<small class="text-muted">
										{x2;if:v:log['action'] == 'config_error'}
										详细错误：{x2;implode:', ',v:log['data']}
										{x2;endif}
									</small>
									{x2;endif}
									{x2;if:v:log['data']['response_info']['response_time_ms']}
									<small>
										响应时间：
										{x2;if:v:log['data']['response_info']['response_time_ms'] < 5000}
										<span class="response-time-fast">{x2;v:log['data']['response_info']['response_time_ms']}ms</span>
										{x2;elseif:v:log['data']['response_info']['response_time_ms'] < 30000}
										<span class="response-time-medium">{x2;v:log['data']['response_info']['response_time_ms']}ms</span>
										{x2;else}
										<span class="response-time-slow">{x2;v:log['data']['response_info']['response_time_ms']}ms</span>
										{x2;endif}
									</small>
									{x2;endif}
									{x2;if:v:log['data']['success_info']['usage']['total_tokens']}
									<small class="text-muted">
										Token: {x2;v:log['data']['success_info']['usage']['total_tokens']}
										{x2;if:v:log['data']['success_info']['usage']['prompt_cache_hit_tokens']}
										(缓存: {x2;v:log['data']['success_info']['usage']['prompt_cache_hit_tokens']})
										{x2;endif}
									</small>
									{x2;endif}
								</td>
								<td>
									<a href="javascript:void(0);" onclick="showLogDetail('{x2;v:lid}')" class="btn btn-mini">查看</a>
									<!-- 隐藏的详细数据 -->
									<div id="log-detail-{x2;v:lid}" style="display: none;">
										<pre>{x2;json_encode:v:log,256}</pre>
									</div>
								</td>
							</tr>
							{x2;endtree}
						</tbody>
					</table>
					
					<!-- 分页 -->
					{x2;if:$logData['pages']}
					<div class="pagination pagination-centered">
						{x2;$logData['pages']}
					</div>
					{x2;endif}
					{x2;endif}
				</div>
			</div>
{x2;if:!$userhash}
		</div>
	</div>
</div>
{x2;include:footer}
</body>
</html>
{x2;endif}

<!-- 日志详情模态框 -->
<div id="logDetailModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="logDetailModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		<h3 id="logDetailModalLabel">日志详细信息</h3>
	</div>
	<div class="modal-body">
		<pre id="logDetailContent" style="max-height: 400px; overflow-y: auto;"></pre>
	</div>
	<div class="modal-footer">
		<button class="btn" data-dismiss="modal" aria-hidden="true">关闭</button>
	</div>
</div>

<script>
// 显示日志详情
function showLogDetail(logId) {
	var detailDiv = document.getElementById('log-detail-' + logId);
	if (detailDiv) {
		var content = detailDiv.innerHTML;
		document.getElementById('logDetailContent').innerHTML = content;
		$('#logDetailModal').modal('show');
	}
}

// 确认清空日志
function confirmClearLog() {
	if (confirm('确定要清空所有AI日志吗？此操作不可恢复！')) {
		window.location.href = 'index.php?exam-master-questions-ailogs&clearlog=1';
	}
}

// 格式化文件大小显示
{x2;if:$logData['fileSize']}
$(document).ready(function() {
	var fileSize = {x2;$logData['fileSize']};
	var sizeText = '';
	if (fileSize > 1024 * 1024) {
		sizeText = (fileSize / (1024 * 1024)).toFixed(2) + ' MB';
	} else if (fileSize > 1024) {
		sizeText = (fileSize / 1024).toFixed(2) + ' KB';
	} else {
		sizeText = fileSize + ' B';
	}
	$('.alert-info strong:contains("KB")').html(sizeText);
});
{x2;endif}
</script>

<style>
.error {
	background-color: #f2dede !important;
}

.pagination {
	margin: 20px 0;
}

.pagination ul {
	margin: 0;
}

.pagination li {
	display: inline;
}

.pagination li a,
.pagination li span {
	float: left;
	padding: 4px 12px;
	line-height: 20px;
	text-decoration: none;
	background-color: #ffffff;
	border: 1px solid #dddddd;
	border-left-width: 0;
}

.pagination li:first-child a,
.pagination li:first-child span {
	border-left-width: 1px;
	-webkit-border-radius: 4px 0 0 4px;
	-moz-border-radius: 4px 0 0 4px;
	border-radius: 4px 0 0 4px;
}

.pagination li:last-child a,
.pagination li:last-child span {
	-webkit-border-radius: 0 4px 4px 0;
	-moz-border-radius: 0 4px 4px 0;
	border-radius: 0 4px 4px 0;
}

.pagination li a:hover {
	background-color: #f5f5f5;
}

.pagination .active a,
.pagination .active span {
	color: #999999;
	cursor: default;
	background-color: #f7f7f7;
	border-color: #dddddd;
}

pre {
	font-size: 11px;
	color: #333;
	white-space: pre-wrap;
	word-wrap: break-word;
}

.form-inline .control-label {
	display: inline-block;
	margin-right: 5px;
	font-weight: bold;
}

.title {
	margin-bottom: 20px;
}

.well {
	margin-bottom: 20px;
}

/* 新增样式 */
.table td {
	vertical-align: middle;
	max-width: 300px;
	word-wrap: break-word;
}

.table td:nth-child(5) {
	max-width: 400px;
}

.text-muted {
	color: #999;
	font-size: 12px;
}

.label {
	font-size: 10px;
	padding: 2px 6px;
}

.btn-mini {
	padding: 2px 6px;
	font-size: 11px;
}

.modal-body pre {
	max-height: 500px;
	overflow-y: auto;
	font-size: 12px;
	line-height: 1.4;
}

.alert-info strong {
	font-weight: bold;
}

/* 响应时间高亮 */
.response-time-fast {
	color: #5cb85c;
	font-weight: bold;
}

.response-time-medium {
	color: #f0ad4e;
	font-weight: bold;
}

.response-time-slow {
	color: #d9534f;
	font-weight: bold;
}

/* 用户标识 */
.user-badge {
	background-color: #5bc0de;
	color: white;
	padding: 1px 6px;
	border-radius: 3px;
	font-size: 11px;
}

/* 消息文本优化 */
.log-message {
	line-height: 1.3;
	font-size: 13px;
}

.log-message small {
	display: block;
	margin-top: 3px;
	font-size: 11px;
	color: #666;
}
</style> 