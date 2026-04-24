<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>数据分析结果 - 在线考试系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" type="text/css" href="files/public/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="files/public/css/datetimepicker.css" />
    <link rel="stylesheet" type="text/css" href="files/public/css/jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="files/public/css/pe.master.css" />
</head>
<body>
    <!-- 引入系统头部导航 -->
    {x2;if:!$userhash}
    {x2;include:nav}
    { x2;endif}
    
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="main">
                {x2;if:!$userhash}
                <div class="col-xs-2 leftmenu">
                    { x2;include:menu}
                </div>
                { x2;endif}
                
                <div id="datacontent" class="{x2;if:!$userhash}col-xs-10{ x2;else}col-xs-12{ x2;endif}">
				<div class="box itembox" style="margin-bottom:0px;border-bottom:1px solid #CCCCCC;">
					<div class="col-xs-12">
						<ol class="breadcrumb">
							<li><a href="index.php?{ x2;$_app}-master">{ x2;$apps[$_app]['appname']}</a></li>
							<li><a href="index.php?dataanalysis-master">数据分析</a></li>
							<li class="active">分析结果</li>
						</ol>
					</div>
				</div>
				<div class="box itembox" style="padding-top:10px;margin-bottom:0px;">
					<h4 class="title" style="padding:10px;">
						<i class="glyphicon glyphicon-stats text-success"></i> 考试数据分析结果
						<button class="btn btn-success pull-right" onclick="exportData()">
							<i class="glyphicon glyphicon-download"></i> 导出数据
						</button>
						<button class="btn btn-primary pull-right" onclick="window.location.href='index.php?dataanalysis-master'" style="margin-right:10px;">
							<i class="glyphicon glyphicon-arrow-left"></i> 返回筛选
						</button>
					</h4>
					
					<!-- 筛选条件显示 -->
					<div class="alert alert-info">
						<strong>当前筛选条件：</strong>
						用户组：{ x2;if:$params['group_id']}{ x2;$usergroups[$params['group_id']]['groupname']}{ x2;else}全部用户组{ x2;endif} | 
						考场：{ x2;if:$params['exam_id']}{ x2;$examrooms[array_search($params['exam_id'], array_column($examrooms, 'examid'))]['exam']}{ x2;else}全部考场{ x2;endif} | 
						时间范围：{ x2;if:$params['start_time']}{ x2;$params['start_time']}{ x2;else}不限{ x2;endif} 至 { x2;if:$params['end_time']}{ x2;$params['end_time']}{ x2;else}不限{ x2;endif}
					</div>
					
					{x2;if:$examData}
					<!-- 总体统计 -->
					<div class="row" style="margin-bottom:20px;">
						<div class="col-md-3">
							<div class="panel panel-primary">
								<div class="panel-heading">
									<h3 class="panel-title">总考试场次</h3>
								</div>
								<div class="panel-body text-center">
									<h1>{ x2;count($examData)}</h1>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="panel panel-success">
								<div class="panel-heading">
									<h3 class="panel-title">总参考人数</h3>
								</div>
								<div class="panel-body text-center">
									<h1>{ x2;array_sum(array_column($examData, 'total_users'))}</h1>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="panel panel-info">
								<div class="panel-heading">
									<h3 class="panel-title">平均分</h3>
								</div>
								<div class="panel-body text-center">
									<h1>{ x2;round(array_sum(array_column($examData, 'avg_score'))/count($examData), 2)}</h1>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="panel panel-warning">
								<div class="panel-heading">
									<h3 class="panel-title">平均及格率</h3>
								</div>
								<div class="panel-body text-center">
									<h1>{ x2;round(array_sum(array_column($examData, 'pass_rate'))/count($examData), 2)}%</h1>
								</div>
							</div>
						</div>
					</div>
					
					<!-- 考试详细数据表格 -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">考试详细数据</h3>
						</div>
						<div class="panel-body">
							<div class="table-responsive">
								<table class="table table-bordered table-striped">
									<thead>
										<tr>
											<th>考试名称</th>
											<th>科目</th>
											<th>参考人数</th>
											<th>平均分</th>
											<th>最高分</th>
											<th>最低分</th>
											<th>及格率</th>
											<th>优秀率</th>
											<th>操作</th>
										</tr>
									</thead>
									<tbody>
										{ x2;foreach:$examData as $exam}
										<tr>
											<td>{ x2;$exam['exam']}</td>
											<td>{ x2;$exam['examsubject']}</td>
											<td>{ x2;$exam['total_users']}</td>
											<td>{ x2;$exam['avg_score']}</td>
											<td>{ x2;$exam['max_score']}</td>
											<td>{ x2;$exam['min_score']}</td>
											<td>{ x2;$exam['pass_rate']}%</td>
											<td>{ x2;$exam['excellent_rate']}%</td>
											<td>
												<button class="btn btn-sm btn-info" onclick="showExamDetail({ x2;$exam['examid']})">
													<i class="glyphicon glyphicon-eye-open"></i> 查看详情
												</button>
											</td>
										</tr>
										{ x2;endforeach}
									</tbody>
								</table>
							</div>
						</div>
					</div>
					
					<!-- 图表展示 -->
					<div class="row">
						<div class="col-md-6">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">分数段分布</h3>
								</div>
								<div class="panel-body">
									<div id="scoreChart" style="height:400px;"></div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">考试趋势</h3>
								</div>
								<div class="panel-body">
									<div id="trendChart" style="height:400px;"></div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">用户组对比</h3>
								</div>
								<div class="panel-body">
									<div id="groupChart" style="height:400px;"></div>
								</div>
							</div>
						</div>
					</div>
					{ x2;else}
					<div class="alert alert-warning">
						<i class="glyphicon glyphicon-warning-sign"></i> 
						<strong>提示：</strong>根据当前筛选条件未找到相关考试数据，请调整筛选条件后重新查询。
					</div>
					{ x2;endif}
					</div>
            </div>
        </div>
    </div>
    
    <!-- 引入系统底部 -->
    {x2;if:!$userhash}
    {x2;include:footer}
    { x2;endif}
    
    <!-- 在页面底部加载JavaScript，确保所有DOM元素都已加载 -->
    <script src="files/public/js/jquery.min.js"></script>
    <script src="files/public/js/jquery-ui.min.js"></script>
    <script src="files/public/js/bootstrap.min.js"></script>
    <script src="files/public/js/bootstrap-datetimepicker.js"></script>
    <script src="files/public/js/all.fine-uploader.min.js"></script>
    <script src="files/public/js/ckeditor/ckeditor.js"></script>
    <script src="files/public/js/echarts/echarts.min.js"></script>
    <script src="files/public/js/pe.master.js"></script>
    
    <script>
    // 确保jQuery加载完成后再执行
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM加载完成');
        
        // 检查jQuery是否加载
        if (typeof jQuery === 'undefined') {
            console.error('jQuery 未加载');
            return;
        }
        
        console.log('jQuery 已加载，版本：' + jQuery.fn.jquery);
        
        // 等待jQuery ready
        jQuery(function($) {
            console.log('jQuery ready 函数执行');
            initCharts();
        });
    });

function initCharts() {
	// 分数段分布图
	var scoreChart = echarts.init(document.getElementById('scoreChart'));
	var scoreOption = {
		title: {
			text: '分数段分布统计'
		},
		tooltip: {
			trigger: 'axis'
		},
		legend: {
			data: ['人数']
		},
		xAxis: {
			type: 'category',
			data: ['不及格', '及格', '良好', '优秀', '卓越']
		},
		yAxis: {
			type: 'value'
		},
		series: [{
			name: '人数',
			type: 'bar',
			data: [
				{ x2;if:$examData && $examData[0]['score_distribution']}{ x2;$examData[0]['score_distribution']['fail_count']}{ x2;else}0{ x2;endif},
				{ x2;if:$examData && $examData[0]['score_distribution']}{ x2;$examData[0]['score_distribution']['pass_count']}{ x2;else}0{ x2;endif},
				{ x2;if:$examData && $examData[0]['score_distribution']}{ x2;$examData[0]['score_distribution']['good_count']}{ x2;else}0{ x2;endif},
				{ x2;if:$examData && $examData[0]['score_distribution']}{ x2;$examData[0]['score_distribution']['very_good_count']}{ x2;else}0{ x2;endif},
				{ x2;if:$examData && $examData[0]['score_distribution']}{ x2;$examData[0]['score_distribution']['excellent_count']}{ x2;else}0{ x2;endif}
			]
		}]
	};
	scoreChart.setOption(scoreOption);
	
	// 考试趋势图
	var trendChart = echarts.init(document.getElementById('trendChart'));
	var trendOption = {
		title: {
			text: '考试趋势分析'
		},
		tooltip: {
			trigger: 'axis'
		},
		legend: {
			data: ['平均分', '参考人数']
		},
		xAxis: {
			type: 'category',
			data: [{ x2;foreach:$trendData as $trend}'{ x2;$trend['exam_date']}',{ x2;endforeach}]
		},
		yAxis: [
			{
				type: 'value',
				name: '平均分'
			},
			{
				type: 'value',
				name: '人数'
			}
		],
		series: [
			{
				name: '平均分',
				type: 'line',
				data: [{ x2;foreach:$trendData as $trend}{ x2;$trend['daily_avg_score']},{ x2;endforeach}]
			},
			{
				name: '参考人数',
				type: 'bar',
				yAxisIndex: 1,
				data: [{ x2;foreach:$trendData as $trend}{ x2;$trend['daily_users']},{ x2;endforeach}]
			}
		]
	};
	trendChart.setOption(trendOption);
	
	// 用户组对比图
	var groupChart = echarts.init(document.getElementById('groupChart'));
	var groupOption = {
		title: {
			text: '用户组对比分析'
		},
		tooltip: {
			trigger: 'axis'
		},
		legend: {
			data: ['平均分', '及格率']
		},
		xAxis: {
			type: 'category',
			data: [{ x2;foreach:$groupData as $group}'{ x2;$group['groupname']}',{ x2;endforeach}]
		},
		yAxis: [
			{
				type: 'value',
				name: '平均分'
			},
			{
				type: 'value',
				name: '及格率(%)'
			}
		],
		series: [
			{
				name: '平均分',
				type: 'bar',
				data: [{ x2;foreach:$groupData as $group}{ x2;$group['avg_score']},{ x2;endforeach}]
			},
			{
				name: '及格率',
				type: 'line',
				yAxisIndex: 1,
				data: [{ x2;foreach:$groupData as $group}{ x2;$group['pass_rate']},{ x2;endforeach}]
			}
		]
	};
	groupChart.setOption(groupOption);
	
	// 响应式调整
	window.addEventListener('resize', function() {
		scoreChart.resize();
		trendChart.resize();
		groupChart.resize();
	});
}

function showExamDetail(examId) {
	// 显示考试详情，可以扩展为模态框或新页面
	alert('考试详情功能开发中... (考试ID: ' + examId + ')');
}

function exportData() {
	var params = {
		group_id: '{ x2;$params['group_id']}',
		exam_id: '{ x2;$params['exam_id']}',
		start_time: '{ x2;$params['start_time']}',
		end_time: '{ x2;$params['end_time']}'
	};
	
	var queryString = $.param(params);
	window.location.href = 'index.php?dataanalysis-master-export&' + queryString;
}
    </script>
</body>
</html>