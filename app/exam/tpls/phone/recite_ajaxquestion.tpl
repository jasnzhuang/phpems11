<div class="page-header">
	<div class="col-1" onclick="javascript:history.back();"><span class="fa fa-chevron-left"></span></div>
	<div class="col-8">{x2;$knows['knows']}</div>
	<div class="col-1"><span class="fa fa-menu hide"></span></div>
</div>
<div class="page-content header footer" data-refresh="yes">
	<form class="list-box bg">
		<ol>
			<li class="unstyled">
				<h4 class="title">
					第 {x2;$number} 题 【{x2;$questype['questype']}】
				</h4>
			</li>
            {x2;if:$parent}
            <li class="unstyled">
				<div class="rows">
					<p>{x2;realhtml:$parent['qrquestion']}</p>
				</div>
			</li>
			{x2;endif}
			<li class="unstyled">
				<div class="rows">
					<p>{x2;realhtml:$question['question']}</p>
				</div>
			</li>
            {x2;if:!$questype['questsort'] && $questype['questchoice'] != 5}
			<li class="unstyled">
				<div class="rows">
					<p>{x2;realhtml:$question['questionselect']}</p>
				</div>
			</li>
			{x2;endif}
			<li class="unstyled rightanswer">
				<div class="rows">
                    {x2;if:$questype['questsort']}
					<div class="intro">
						<span class="badge">正确答案</span>
					</div>
					<div class="intro">
						{x2;realhtml:$question['questionanswer']}
					</div>
					{x2;else}
					<div class="col-4x intro">
						<span class="badge">正确答案</span>
					</div>
					<div class="col-4l intro">
						<b id="rightanswer_{x2;$question['questionid']}">{x2;$question['questionanswer']}</b>
					</div>
					{x2;endif}
				</div>
			</li>
			<li class="unstyled rightanswer">
				<div class="rows">
					{x2;if:strlen($question['questiondescribe']) >= 10}
					<div class="intro">
						<span class="badge">试题解析</span>
					</div>
					<div class="intro">
						{x2;realhtml:$question['questiondescribe']}
					</div>
					{x2;else}
					<div class="col-4x">
						<span class="badge">试题解析</span>
					</div>
					<div class="col-4l intro">
                        {x2;realhtml:$question['questiondescribe']}
					</div>
					{x2;endif}
				</div>
			</li>
			<li class="unstyled"></li>
		</ol>
	</form>
</div>
<div class="page-footer">
	<ol class="pagination">
		<li class="col-8x{x2;if:$number > 1} jump prev" data-number="{x2;eval: echo $number - 1}{x2;endif}">
            <span class="fa fa-chevron-circle-left"></span>
		</li>
		<li class="col-8x favor" data-questionid="{x2;$question['questionid']}">
			<span class="fa fa-star"></span>
		</li>
		<li class="col-5">
			<form method="post" data-target="questionpanel" action="index.php?exam-app-recite-ajax-questions&knowsid={x2;$knows['knowsid']}">
				共{x2;$allnumber}题，转到 <input class="text-center" name="number" placeholder="{x2;$number}" type="search" size="3"> 题
			</form>
		</li>
		<li class="col-8x">
			<a href="index.php?exam-phone-recite-reporterror&questionid={x2;$question['questionid']}" class="ajax">
				<span class="fa fa-wrench"></span>
			</a>
		</li>
		<li class="col-8x{x2;if:$number < $allnumber} jump next" data-number="{x2;eval: echo $number + 1}{x2;endif}">
			<span class="fa fa-chevron-circle-right"></span>
		</li>
	</ol>
</div>
<script type="text/javascript">
    $(function(){
        setTimeout(function(){
			var tapbox = $(".page-content").first();
			var x,offx,y,offy;
			tapbox.on("touchstart",function(event){
				x = event.clientX || event.originalEvent.changedTouches[0].clientX;
				y = event.clientY || event.originalEvent.changedTouches[0].clientY;
			});
			tapbox.on("touchend",function(event){
				offx = event.originalEvent.changedTouches[0].clientX - x;
				offy = event.originalEvent.changedTouches[0].clientY - y;
				if(Math.abs(offx) > Math.abs(offy))
				{
					if(offx > 5)$('#questionpanel .jump.prev').trigger("click");
					if(offx < -5)$('#questionpanel .jump.next').trigger("click");
				}
				offx = 0;
			});
		},100);
        $('#questionpanel .favor').on('click',function() {
                favorquestion($(this).attr('data-questionid'));
            });
		$('#questionpanel .jump').on('click',function(){
			var number = parseInt($(this).attr('data-number'));
			if(number <= 0)return ;
			submitAjax({url:"index.php?exam-phone-recite-ajax-questions&knowsid={x2;$knows['knowsid']}&number="+number,"target":"questionpanel"});
			});
    });
</script>
