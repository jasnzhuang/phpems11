{x2;if:!$userhash}
{x2;include:header}
<body>
{x2;include:nav}
<div class="container-fluid">
	<div class="row-fluid">
		<div class="main">
			<div class="col-xs-2 leftmenu">
{x2;endif}
				<div class="box itembox" style="margin-bottom:0px;border-bottom:1px solid #CCCCCC;">
					<div class="col-xs-12">
						<ol class="breadcrumb">
							<li><a href="index.php?dataanalysis-master">数据分析</a></li>
							<li class="active">菜单</li>
						</ol>
					</div>
				</div>
				<div class="box itembox" style="padding-top:10px;margin-bottom:0px;">
					<h4 class="title" style="padding:10px;">
						<i class="glyphicon glyphicon-stats text-success"></i> 数据分析
					</h4>
					<ul class="nav nav-pills nav-stacked">
						<li>
							<a href="index.php?dataanalysis-master">
								<i class="glyphicon glyphicon-home"></i> 数据分析首页
							</a>
						</li>
						<li>
							<a href="index.php?dataanalysis-master-analysis">
								<i class="glyphicon glyphicon-search"></i> 考试数据分析
							</a>
						</li>
						<li>
							<a href="index.php?dataanalysis-master-export">
								<i class="glyphicon glyphicon-download"></i> 数据导出
							</a>
						</li>
					</ul>
				</div>
{x2;if:!$userhash}
			</div>
		</div>
	</div>
</div>
{x2;include:footer}
{x2;endif}