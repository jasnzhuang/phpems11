{ x2;if:!$userhash}
{ x2;include:header}
<body>
{ x2;include:nav}
<div class="container-fluid">
	<div class="row-fluid">
		<div class="main">
			<div class="col-xs-2 leftmenu">
{ x2;include:menu}
			</div>
			<div id="datacontent">
{ x2;endif}
				<div class="box itembox" style="margin-bottom:0px;border-bottom:1px solid #CCCCCC;">
					<div class="col-xs-12">
						<ol class="breadcrumb">
							<li><a href="index.php?{ x2;$_app}-master">{ x2;$apps[$_app]['appname']}</a></li>
							<li class="active">数据分析</li>
						</ol>
					</div>
				</div>
				<div class="box itembox" style="padding-top:10px;margin-bottom:0px;">
					<h4 class="title" style="padding:10px;">
						<i class="glyphicon glyphicon-stats text-success"></i> 考试数据分析
					</h4>
					<div class="alert alert-info">
						<i class="glyphicon glyphicon-info-sign"></i> 
						<strong>功能说明：</strong>通过筛选条件对考试数据进行统计分析，生成详细的考试分析报告和可视化图表。
					</div>
					
					<form action="index.php?dataanalysis-master-analysis" method="post" class="form-horizontal">
						<div class="form-group">
							<label class="col-sm-2 control-label">用户组：</label>
							<div class="col-sm-4">
								<select name="group_id" class="form-control">
									<option value="0">全部用户组</option>
									{ x2;foreach:$usergroups as $group}
									<option value="{ x2;$group['groupid']}">{ x2;$group['groupname']}</option>
									{ x2;endforeach}
								</select>
							</div>
							<label class="col-sm-2 control-label">考场：</label>
							<div class="col-sm-4">
								<select name="exam_id" class="form-control">
									<option value="0">全部考场</option>
									{ x2;foreach:$examrooms as $room}
									<option value="{ x2;$room['examid']}">{ x2;$room['exam']}</option>
									{ x2;endforeach}
								</select>
							</div>
						</div>
						
						<div class="form-group">
							<label class="col-sm-2 control-label">开始时间：</label>
							<div class="col-sm-4">
								<input type="text" name="start_time" class="form-control datetimepicker" placeholder="选择开始时间">
							</div>
							<label class="col-sm-2 control-label">结束时间：</label>
							<div class="col-sm-4">
								<input type="text" name="end_time" class="form-control datetimepicker" placeholder="选择结束时间">
							</div>
						</div>
						
						<div class="form-group">
							<div class="col-sm-offset-2 col-sm-10">
								<button type="submit" class="btn btn-primary">
									<i class="glyphicon glyphicon-search"></i> 开始分析
								</button>
								<button type="button" class="btn btn-default" onclick="resetForm()">
									<i class="glyphicon glyphicon-refresh"></i> 重置
								</button>
							</div>
						</div>
					</form>
				</div>
{ x2;if:!$userhash}
			</div>
		</div>
	</div>
</div>
{ x2;include:footer}

<!-- 确保jQuery加载后再执行自定义脚本 -->
<script>
// 检查jQuery是否加载
if (typeof jQuery === 'undefined') {
    console.error('jQuery 未加载');
} else {
    console.log('jQuery 已加载，版本：' + jQuery.fn.jquery);
    
    $(function(){
        // 检查datetimepicker插件是否加载
        if ($.fn.datetimepicker) {
            $('.datetimepicker').datetimepicker({
                format: 'yyyy-mm-dd',
                weekStart: 1,
                todayBtn:  1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                minView: 2,
                forceParse: 0
            });
        } else {
            console.error('datetimepicker 插件未加载');
        }
    });
}

function resetForm() {
    if (typeof jQuery !== 'undefined') {
        jQuery('form')[0].reset();
    } else {
        console.error('jQuery 未加载，无法重置表单');
    }
}
</script>
{ x2;endif}
</body>
</html>