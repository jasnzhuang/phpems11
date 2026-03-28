{x2;if:!$userhash}
{x2;include:header}
<body>
<div class="pages">
    {x2;endif}
	<div class="page-tabs">
		<div class="page-header">
			<div class="col-1" onclick="javascript:history.back();"><span class="fa fa-chevron-left"></span></div>
			<div class="col-8">{x2;$data['currentbasic']['basic']}</div>
			<div class="col-1"><span class="fa fa-menu hide"></span></div>
		</div>
		<div class="page-content header">
			<div class="list-box bg">
				<ol>
					{x2;tree:$menus,menu,mid}
					{x2;if:in_array($data['currentbasic']['basicexam']['model'],v:menu['basictype'])}
					<li class="unstyled">
						<div class="col-2">
							<div class="rows illus">
								<i class="{x2;v:menu['icon']} examicon"></i>
							</div>
						</div>
						<div class="col-8">
							<a href="{x2;v:menu['url']}" class="ajax">
								<div class="rows info">
									<h5 class="title">{x2;v:menu['title']}</h5>
									<p class="intro">{x2;v:menu['intro']}</p>
								</div>
							</a>
						</div>
					</li>
					{x2;endif}
					{x2;endtree}
				</ol>
			</div>
		</div>
	</div>
    {x2;if:!$userhash}
</div>
</body>
</html>
{x2;endif}