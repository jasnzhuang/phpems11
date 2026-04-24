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
							<li><a href="index.php?exam-master-basic">考场管理</a></li>
							<li class="active">成绩分析</li>
						</ol>
					</div>
				</div>
				
				<div class="box itembox" style="padding-top:10px;margin-bottom:0px;">
					<h4 class="title" style="padding:10px;">
						<i class="glyphicon glyphicon-stats text-success"></i> 
						{x2;$basic['basic']} - 成绩分析
						<button class="btn btn-primary pull-right" onclick="window.close()">
							<i class="glyphicon glyphicon-remove"></i> 关闭
						</button>
					</h4>
					
					<!-- 考场基本信息 -->
					<div class="alert alert-info">
						<h5><i class="glyphicon glyphicon-info-sign"></i> 考场信息</h5>
						<p><strong>考场名称：</strong>{x2;$basic['basic']}</p>
						<p><strong>考试科目：</strong>{x2;$subjects[$basic['basicsubjectid']]['subject']}</p>
						<p><strong>考场地区：</strong>{x2;$areas[$basic['basicareaid']]['area']}</p>
						<p><strong>状态：</strong>{x2;if:$basic['basicclosed']}关闭{x2;else}开启{x2;endif}</p>
					</div>
					
										
					<!-- 分析结果 -->
					{x2;if:$examData}
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">
								<i class="glyphicon glyphicon-chart"></i> 
								考试成绩统计分析
							</h3>
						</div>
						<div class="panel-body">
							<!-- 总体统计 -->
							<div class="row" style="margin-bottom: 20px;">
								<div class="col-md-2">
									<div class="panel panel-primary">
										<div class="panel-heading text-center">
											<h4>总考试次数</h4>
										</div>
										<div class="panel-body text-center">
											<h2>{x2;$totalAttempts}</h2>
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="panel panel-success">
										<div class="panel-heading text-center">
											<h4>参考人数</h4>
										</div>
										<div class="panel-body text-center">
											<h2>{x2;$totalUsers}</h2>
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="panel panel-info">
										<div class="panel-heading text-center">
											<h4>平均及格率</h4>
										</div>
										<div class="panel-body text-center">
											<h2>{x2;$avgPassRate}%</h2>
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="panel panel-warning">
										<div class="panel-heading text-center">
											<h4>平均优秀率</h4>
										</div>
										<div class="panel-body text-center">
											<h2>{x2;$avgExcellentRate}%</h2>
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="panel panel-default">
										<div class="panel-heading text-center">
											<h4>考场总人数</h4>
										</div>
										<div class="panel-body text-center">
											<h2>{x2;$totalUsersInBasic}</h2>
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="panel panel-danger">
										<div class="panel-heading text-center">
											<h4>参与率</h4>
										</div>
										<div class="panel-body text-center">
											<h2>{x2;$participationRate}%</h2>
										</div>
									</div>
								</div>
							</div>
							
							<!-- 详细数据表格 -->
							<table class="table table-bordered table-striped table-hover">
								<thead>
									<tr class="success">
										<th>考试名称</th>
										<th>科目</th>
										<th>参考人数</th>
										<th>考试次数</th>
										<th>平均分</th>
										<th>最高分</th>
										<th>最低分</th>
										<th>及格率</th>
										<th>优秀率</th>
									</tr>
								</thead>
								<tbody>
									{x2;tree:$examData,exam,eid}
									<tr>
										<td>{x2;v:exam['exam']}</td>
										<td>{x2;v:exam['examsubject']}</td>
										<td>{x2;v:exam['total_users']}</td>
										<td>{x2;v:exam['total_attempts']}</td>
										<td>{x2;v:exam['avg_score']}</td>
										<td>{x2;v:exam['max_score']}</td>
										<td>{x2;v:exam['min_score']}</td>
										<td>
											<div class="progress" style="margin-bottom: 0;">
												<div class="progress-bar {x2;if:v:exam['pass_rate'] >= 80}progress-bar-success{x2;elseif:v:exam['pass_rate'] >= 60}progress-bar-warning{x2;else}progress-bar-danger{x2;endif}" 
												     style="width: {x2;v:exam['pass_rate']}%" 
												     title="{x2;v:exam['pass_rate']}%">
													{x2;v:exam['pass_rate']}%
												</div>
											</div>
										</td>
										<td>
											<div class="progress" style="margin-bottom: 0;">
												<div class="progress-bar {x2;if:v:exam['excellent_rate'] >= 70}progress-bar-success{x2;elseif:v:exam['excellent_rate'] >= 40}progress-bar-warning{x2;else}progress-bar-danger{x2;endif}" 
												     style="width: {x2;v:exam['excellent_rate']}%" 
												     title="{x2;v:exam['excellent_rate']}%">
													{x2;v:exam['excellent_rate']}%
												</div>
											</div>
										</td>
									</tr>
									{x2;endtree}
								</tbody>
							</table>
							
							<!-- 成绩分布图 -->
							<div id="scoreChart" style="width: 100%; height: 400px; margin-top: 20px;"></div>
							
							<!-- 调试信息 -->
					<div class="alert alert-warning">
						<h5><i class="glyphicon glyphicon-info-sign"></i> 时间数据状态说明</h5>
						<p><strong>总考试记录数:</strong> {x2;$debugInfo['total_history']}</p>
						<p><strong>有效时间记录:</strong> {x2;$debugInfo['valid_time_records']}</p>
						<p><strong>缺失时间记录:</strong> {x2;$debugInfo['incomplete_records']}</p>
						<p><small>注：系统使用ehtime字段记录考试用时（秒数），ehstarttime记录考试开始时间。</small></p>
					</div>
					
					<!-- 详细用户统计 - 按用户组分组 -->
							<div class="row" style="margin-top: 30px;">
								<div class="col-md-6">
									<div class="panel panel-success">
										<div class="panel-heading">
											<h4><i class="glyphicon glyphicon-user"></i> 已参加考试用户 ({x2;$participatedCount}人)</h4>
										</div>
										<div class="panel-body">
											{x2;tree:$participatedUsersByGroup,users,key}
											<div class="group-section" style="margin-bottom: 25px;">
												<h5 class="text-primary" style="border-bottom: 2px solid #5cb85c; padding-bottom: 5px;">
													<i class="glyphicon glyphicon-tag"></i> {x2;v:key} ({x2;$participatedGroupCounts[v:key]}人)
												</h5>
												<div class="table-responsive">
													<table class="table table-striped table-condensed">
														<thead>
															<tr>
																<th>用户名</th>
																<th>真实姓名</th>
																<th>考试次数</th>
																<th>平均分</th>
																<th>最高分</th>
																<th>最低分</th>
																<th>考试用时</th>
																<th>及格率</th>
															</tr>
														</thead>
														<tbody>
															{x2;tree:v:users,user,uid}
															<tr>
																<td>{x2;v:user['username']}</td>
																<td>{x2;v:user['usertruename']}</td>
																<td>{x2;v:user['stats']['attempts']}</td>
																<td>{x2;v:user['stats']['avg_score']}</td>
																<td>{x2;v:user['stats']['max_score']}</td>
																<td>{x2;v:user['stats']['min_score']}</td>
																<td>{x2;v:user['stats']['avg_duration']}</td>
																<td>
																	<div class="progress" style="margin-bottom: 0;">
																		<div class="progress-bar {x2;if:v:user['stats']['pass_rate'] >= 80}progress-bar-success{x2;elseif:v:user['stats']['pass_rate'] >= 60}progress-bar-warning{x2;else}progress-bar-danger{x2;endif}" 
																		     style="width: {x2;v:user['stats']['pass_rate']}%" 
																		     title="{x2;v:user['stats']['pass_rate']}%">
																			{x2;v:user['stats']['pass_rate']}%
																		</div>
																	</div>
																</td>
															</tr>
															{x2;endtree}
														</tbody>
													</table>
												</div>
											</div>
											{x2;endtree}
											
											{x2;if:!$participatedUsersByGroup}
											<div class="alert alert-info">
												<i class="glyphicon glyphicon-info-sign"></i> 暂无已参加考试用户
											</div>
											{x2;endif}
										</div>
									</div>
								</div>
								
								<div class="col-md-6">
									<div class="panel panel-danger">
										<div class="panel-heading">
											<h4><i class="glyphicon glyphicon-user"></i> 未参加考试用户 ({x2;$notParticipatedCount}人)</h4>
										</div>
										<div class="panel-body">
											{x2;tree:$notParticipatedUsersByGroup,users,key}
											<div class="group-section" style="margin-bottom: 25px;">
												<h5 class="text-danger" style="border-bottom: 2px solid #d9534f; padding-bottom: 5px;">
													<i class="glyphicon glyphicon-tag"></i> {x2;v:key} ({x2;$notParticipatedGroupCounts[v:key]}人)
												</h5>
												<div class="table-responsive">
													<table class="table table-striped table-condensed">
														<thead>
															<tr>
																<th>用户名</th>
																<th>真实姓名</th>
																<th>邮箱</th>
																<th>注册时间</th>
															</tr>
														</thead>
														<tbody>
															{x2;tree:v:users,user,uid}
															<tr>
																<td>{x2;v:user['username']}</td>
																<td>{x2;v:user['usertruename']}</td>
																<td>{x2;v:user['useremail']}</td>
																<td>{x2;date('Y-m-d', v:user['userregtime'])}</td>
															</tr>
															{x2;endtree}
														</tbody>
													</table>
												</div>
											</div>
											{x2;endtree}
											
											{x2;if:!$notParticipatedUsersByGroup}
											<div class="alert alert-info">
												<i class="glyphicon glyphicon-info-sign"></i> 暂无未参加考试用户
											</div>
											{x2;endif}
										</div>
									</div>
								</div>
							</div>
							
							<!-- 异常分析 -->
							<div class="row" style="margin-top: 30px;">
								<div class="col-md-12">
									<div class="panel panel-warning">
										<div class="panel-heading">
											<h4><i class="glyphicon glyphicon-exclamation-sign"></i> 异常考试数据分析</h4>
										</div>
										<div class="panel-body">
											{x2;if:$anomalies['fast_high_scores']}
											<div class="alert alert-danger">
												<h5><i class="glyphicon glyphicon-time"></i> 快速高分异常 (小于5分钟取得80分以上)</h5>
												<div class="table-responsive">
													<table class="table table-condensed">
														<thead>
															<tr>
																<th>用户ID</th>
																<th>分数</th>
																<th>用时</th>
																<th>考试ID</th>
															</tr>
														</thead>
														<tbody>
															{x2;tree:$anomalies['fast_high_scores'],anomaly,aid}
															<tr>
																<td>{x2;v:anomaly['userid']}</td>
																<td>{x2;v:anomaly['score']}</td>
																<td>{x2;v:anomaly['duration']}</td>
																<td>{x2;v:anomaly['exam']}</td>
															</tr>
															{x2;endtree}
														</tbody>
													</table>
												</div>
											</div>
											{x2;endif}
											
											{x2;if:$anomalies['long_low_scores']}
											<div class="alert alert-warning">
												<h5><i class="glyphicon glyphicon-time"></i> 长时低分异常 (大于30分钟仍未及格)</h5>
												<div class="table-responsive">
													<table class="table table-condensed">
														<thead>
															<tr>
																<th>用户ID</th>
																<th>分数</th>
																<th>用时</th>
																<th>考试ID</th>
															</tr>
														</thead>
														<tbody>
															{x2;tree:$anomalies['long_low_scores'],anomaly,aid}
															<tr>
																<td>{x2;v:anomaly['userid']}</td>
																<td>{x2;v:anomaly['score']}</td>
																<td>{x2;v:anomaly['duration']}</td>
																<td>{x2;v:anomaly['exam']}</td>
															</tr>
															{x2;endtree}
														</tbody>
													</table>
												</div>
											</div>
											{x2;endif}
											
											{x2;if:!$anomalies['fast_high_scores'] && !$anomalies['long_low_scores']}
											<div class="alert alert-success">
												<h5><i class="glyphicon glyphicon-ok"></i> 未发现异常考试数据</h5>
												<p>所有考试记录均在正常范围内，未检测到快速高分或长时低分等异常情况。</p>
											</div>
											{x2;endif}
										</div>
									</div>
								</div>
							</div>
							
						</div>
					</div>
					{x2;else}
					<div class="alert alert-warning">
						<i class="glyphicon glyphicon-exclamation-sign"></i>
						<strong>暂无考试数据</strong>
						<p>该考场暂无有效的考试成绩数据，请确保有学生已完成考试。</p>
					</div>
					{x2;endif}
					
				</div>
			</div>
		</div>
	</div>
</div>

<!-- JavaScript 文件 -->
<script src="files/public/js/jquery.min.js"></script>
<script src="files/public/js/bootstrap.min.js"></script>
<script src="files/public/js/echarts/echarts.min.js"></script>

<script>
// 页面加载完成后执行
$(document).ready(function() {
	console.log('成绩分析页面加载完成');
	
	// 如果有数据，显示图表
	{x2;if:$examData}
	// 初始化图表
	var chartDom = document.getElementById('scoreChart');
	var myChart = echarts.init(chartDom);
	
	// 准备图表数据
	var examNames = [
		{x2;tree:$examData,exam,eid}'{x2;v:exam['exam']}'{x2;if:!$_GET['eidlast']},{x2;endif}{x2;endtree}
	];
	
	var avgScores = [
		{x2;tree:$examData,exam,eid}{x2;v:exam['avg_score']}{x2;if:!$_GET['eidlast']},{x2;endif}{x2;endtree}
	];
	
	var passRates = [
		{x2;tree:$examData,exam,eid}{x2;v:exam['pass_rate']}{x2;if:!$_GET['eidlast']},{x2;endif}{x2;endtree}
	];
	
	var excellentRates = [
		{x2;tree:$examData,exam,eid}{x2;v:exam['excellent_rate']}{x2;if:!$_GET['eidlast']},{x2;endif}{x2;endtree}
	];
	
	// 图表配置
	var option = {
		title: {
			text: '考试成绩分析图表',
			left: 'center'
		},
		tooltip: {
			trigger: 'axis',
			axisPointer: {
				type: 'cross'
			}
		},
		legend: {
			data: ['平均分', '及格率', '优秀率'],
			top: '30px'
		},
		grid: {
			left: '3%',
			right: '4%',
			bottom: '3%',
			containLabel: true
		},
		xAxis: [
			{
				type: 'category',
				data: examNames,
				axisPointer: {
					type: 'shadow'
				}
			}
		],
		yAxis: [
			{
				type: 'value',
				name: '分数',
				min: 0,
				max: 100,
				interval: 20
			},
			{
				type: 'value',
				name: '百分比',
				min: 0,
				max: 100,
				interval: 20
			}
		],
		series: [
			{
				name: '平均分',
				type: 'bar',
				data: avgScores,
				itemStyle: {
					color: '#5470c6'
				}
			},
			{
				name: '及格率',
				type: 'line',
				yAxisIndex: 1,
				data: passRates,
				itemStyle: {
					color: '#91cc75'
				}
			},
			{
				name: '优秀率',
				type: 'line',
				yAxisIndex: 1,
				data: excellentRates,
				itemStyle: {
					color: '#fac858'
				}
			}
		]
	};
	
	// 设置图表选项
	myChart.setOption(option);
	
	// 窗口大小改变时重新渲染图表
	window.addEventListener('resize', function() {
		myChart.resize();
	});
	
	{x2;endif}
});

// 关闭窗口
function closeWindow() {
	if (confirm('确定要关闭此窗口吗？')) {
		window.close();
	}
}
</script>

{x2;if:!$userhash}
	</div>
</div>
</div>
{x2;include:footer}
</body>
</html>
{x2;endif}