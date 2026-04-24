<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>数据分析 - 在线考试系统</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" type="text/css" href="files/public/css/bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="files/public/css/datetimepicker.css" />
    <link rel="stylesheet" type="text/css" href="files/public/css/jquery-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="files/public/css/pe.master.css" />
    <style>
        .error-box {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .warning-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 15px;
            margin: 10px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <!-- 系统导航 -->
    {x2;if:!$userhash}
    {x2;include:nav}
    {x2;endif}
    
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="main">
                {x2;if:!$userhash}
                <div class="col-xs-2 leftmenu">
                    { x2;include:menu}
                </div>
                { x2;endif}
                
                <div id="datacontent" class="{x2;if:!$userhash}col-xs-10{x2;else}col-xs-12{x2;endif}">
                    <!-- 面包屑导航 -->
                    <div class="box itembox" style="margin-bottom:0px;border-bottom:1px solid #CCCCCC;">
                        <div class="col-xs-12">
                            <ol class="breadcrumb">
                                <li><a href="index.php?{x2;$_app}-master">{x2;$apps[$_app]['appname']}</a></li>
                                <li class="active">数据分析</li>
                            </ol>
                        </div>
                    </div>
                    
                    <!-- 页面标题 -->
                    <div class="box itembox" style="padding-top:10px;margin-bottom:0px;">
                        <h4 class="title" style="padding:10px;">
                            <i class="glyphicon glyphicon-stats text-success"></i> 考试数据分析
                        </h4>
                        
                        <!-- 系统状态提示 -->
                        {x2;if:!$usergroups}
                        <div class="error-box">
                            <i class="glyphicon glyphicon-exclamation-sign"></i>
                            <strong>系统错误：</strong>用户组数据加载失败，请联系管理员检查数据库连接。
                        </div>
                        { x2;endif}
                        
                        {x2;if:!$examrooms}
                        <div class="warning-box">
                            <i class="glyphicon glyphicon-warning-sign"></i>
                            <strong>提示：</strong>考场数据为空，可能暂无考试数据。
                        </div>
                        { x2;endif}
                        
                        <!-- 功能说明 -->
                        <div class="alert alert-info">
                            <i class="glyphicon glyphicon-info-sign"></i> 
                            <strong>功能说明：</strong>通过筛选条件对考试数据进行统计分析，生成详细的考试分析报告和可视化图表。
                        </div>
                        
                        <!-- 筛选表单 -->
                        <form action="index.php?dataanalysis-master-analysis" method="post" class="form-horizontal">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">用户组：</label>
                                <div class="col-sm-4">
                                    <select name="group_id" class="form-control">
                                        <option value="0">全部用户组</option>
                                        {x2;if:$usergroups}
                                            {x2;foreach:$usergroups as $group}
                                                <option value="{x2;$group['groupid']}">{x2;$group['groupname']}</option>
                                            { x2;endforeach}
                                        { x2;else}
                                            <option value="" disabled>无用户组数据</option>
                                        { x2;endif}
                                    </select>
                                </div>
                                <label class="col-sm-2 control-label">考场：</label>
                                <div class="col-sm-4">
                                    <select name="exam_id" class="form-control">
                                        <option value="0">全部考场</option>
                                        {x2;if:$examrooms}
                                            {x2;foreach:$examrooms as $room}
                                                <option value="{x2;$room['examid']}">{x2;$room['exam']}</option>
                                            { x2;endforeach}
                                        { x2;else}
                                            <option value="" disabled>无考场数据</option>
                                        { x2;endif}
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
                                    <button type="button" class="btn btn-info" onclick="showDebugInfo()">
                                        <i class="glyphicon glyphicon-cog"></i> 调试信息
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <!-- 调试信息面板 -->
                        <div id="debug-panel" style="display: none; margin-top: 20px;">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">调试信息</h3>
                                </div>
                                <div class="panel-body">
                                    <div id="debug-content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 系统底部 -->
    {x2;if:!$userhash}
    {x2;include:footer}
    {x2;endif}
    
    <!-- JavaScript加载 -->
    <script src="files/public/js/jquery.min.js"></script>
    <script src="files/public/js/jquery-ui.min.js"></script>
    <script src="files/public/js/bootstrap.min.js"></script>
    <script src="files/public/js/bootstrap-datetimepicker.js"></script>
    <script src="files/public/js/all.fine-uploader.min.js"></script>
    <script src="files/public/js/ckeditor/ckeditor.js"></script>
    <script src="files/public/js/echarts/echarts.min.js"></script>
    <script src="files/public/js/pe.master.js"></script>
    
    <script>
    // 页面加载完成后执行
    document.addEventListener('DOMContentLoaded', function() {
        console.log('数据分析页面开始加载...');
        
        // 检查jQuery是否加载
        if (typeof jQuery === 'undefined') {
            console.error('jQuery 未加载');
            showError('jQuery 库加载失败，请检查网络连接');
            return;
        }
        
        console.log('jQuery 已加载，版本：' + jQuery.fn.jquery);
        
        // jQuery ready
        jQuery(function($) {
            console.log('jQuery ready 函数执行');
            
            // 初始化日期选择器
            try {
                $('.datetimepicker').datetimepicker({
                    format: 'yyyy-mm-dd',
                    weekStart: 1,
                    todayBtn: 1,
                    autoclose: 1,
                    todayHighlight: 1,
                    startView: 2,
                    minView: 2,
                    forceParse: 0,
                    language: 'zh-CN'
                });
                console.log('日期选择器初始化成功');
            } catch (e) {
                console.error('日期选择器初始化失败:', e);
                showError('日期选择器初始化失败: ' + e.message);
            }
            
            // 检查数据加载状态
            checkDataStatus();
        });
    });
    
    // 检查数据状态
    function checkDataStatus() {
        var usergroupsCount = jQuery('{x2;if:$usergroups}{x2;count($usergroups)}{x2;else}0{x2;endif}');
        var examroomsCount = jQuery('{x2;if:$examrooms}{x2;count($examrooms)}{x2;else}0{x2;endif}');
        
        console.log('用户组数量:', usergroupsCount);
        console.log('考场数量:', examroomsCount);
        
        if (usergroupsCount == 0) {
            showWarning('用户组数据为空，可能影响分析结果');
        }
        
        if (examroomsCount == 0) {
            showWarning('考场数据为空，暂无考试数据可分析');
        }
    }
    
    // 重置表单
    function resetForm() {
        try {
            jQuery('form')[0].reset();
            console.log('表单重置成功');
        } catch (e) {
            console.error('表单重置失败:', e);
        }
    }
    
    // 显示调试信息
    function showDebugInfo() {
        var panel = jQuery('#debug-panel');
        var content = jQuery('#debug-content');
        
        if (panel.is(':visible')) {
            panel.hide();
        } else {
            var debugInfo = '<h4>系统状态</h4>';
            debugInfo += '<p><strong>jQuery版本：</strong>' + jQuery.fn.jquery + '</p>';
            debugInfo += '<p><strong>用户组数量：</strong>' + jQuery('{x2;if:$usergroups}{x2;count($usergroups)}{x2;else}0{x2;endif}') + '</p>';
            debugInfo += '<p><strong>考场数量：</strong>' + jQuery('{x2;if:$examrooms}{x2;count($examrooms)}{x2;else}0{x2;endif}') + '</p>';
            debugInfo += '<p><strong>浏览器：</strong>' + navigator.userAgent + '</p>';
            debugInfo += '<p><strong>页面加载时间：</strong>' + new Date().toLocaleString() + '</p>';
            
            content.html(debugInfo);
            panel.show();
        }
    }
    
    // 显示错误信息
    function showError(message) {
        var errorHtml = '<div class="error-box">';
        errorHtml += '<i class="glyphicon glyphicon-exclamation-sign"></i> ';
        errorHtml += '<strong>错误：</strong>' + message;
        errorHtml += '</div>';
        
        jQuery('.form-horizontal').before(errorHtml);
    }
    
    // 显示警告信息
    function showWarning(message) {
        var warningHtml = '<div class="warning-box">';
        warningHtml += '<i class="glyphicon glyphicon-warning-sign"></i> ';
        warningHtml += '<strong>警告：</strong>' + message;
        warningHtml += '</div>';
        
        jQuery('.form-horizontal').before(warningHtml);
    }
    
    // 显示成功信息
    function showSuccess(message) {
        var successHtml = '<div class="success-box">';
        successHtml += '<i class="glyphicon glyphicon-ok-sign"></i> ';
        successHtml += '<strong>成功：</strong>' + message;
        successHtml += '</div>';
        
        jQuery('.form-horizontal').before(successHtml);
    }
    </script>
</body>
</html>