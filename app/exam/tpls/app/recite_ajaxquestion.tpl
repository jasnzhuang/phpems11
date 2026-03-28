		<h2 class="title">
			第 {x2;$number} 题 【{x2;$questype['questype']}】
			<a class="badge pull-right favor" data-questionid="{x2;$question['questionid']}">收藏</a>
			<a class="badge pull-right error" data-questionid="{x2;$question['questionid']}">纠错</a>
            {x2;if:$number < $allnumber}
			<a class="jump next badge pull-right" data-number="{x2;eval: echo $number + 1}">下一题</a>
			{x2;endif}
            {x2;if:$number > 1}
			<a class="jump prev badge pull-right" data-number="{x2;eval: echo $number - 1}">上一题</a>
            {x2;endif}
		</h2>
		<ul class="list-unstyled list-img">
			{x2;if:$parent}
			<li class="border morepadding">
				<div class="desc">
					<p>{x2;realhtml:$parent['qrquestion']}</p>
				</div>
			</li>
            {x2;endif}
			<li class="border morepadding">
				<div class="desc">
					<p>{x2;realhtml:$question['question']}</p>
				</div>
			</li>
			{x2;if:!$questype['questsort'] && $questype['questchoice'] != 5}
			<li class="border morepadding">
				<div class="desc">
					<p>{x2;realhtml:$question['questionselect']}</p>
				</div>
			</li>
			{x2;endif}
			<li class="border morepadding rightanswer">
				<div class="intro">
					<div class="desc">
						<div class="col-xs-1 nopadding">
							<div class="toolbar"><span class="badge">正确答案</span></div>
						</div>
                        {x2;if:$questype['questsort']}
						<div class="col-xs-11">
							{x2;realhtml:$question['questionanswer']}
						</div>
						{x2;else}
						<div class="col-xs-11">
							<b id="rightanswer_{x2;$question['questionid']}">{x2;$question['questionanswer']}</b>
						</div>
						{x2;endif}
					</div>
				</div>
			</li>
			<li class="border morepadding rightanswer">
				<div class="intro">
					<div class="desc">
						<div class="col-xs-1 nopadding">
							<div class="toolbar"><span class="badge">试题解析</span></div>
						</div>
						<div class="col-xs-11">
							{x2;realhtml:$question['questiondescribe']}
						</div>
					</div>
				</div>
			</li>
			<li class="border padding">
				<div class="intro text-right">
					<div class="desc">
						<form class="toolbar" target="questionpanel" action="index.php?exam-app-recite-ajax-questions&knowsid={x2;$knows['knowsid']}">
							共 {x2;$allnumber} 题，当前第 {x2;$number} 题。
							<span class="form-inline form-group">
								去第 <input type="search" size="1" class="form-control text-center" name="number" placeholder="{x2;$number}"> 题
							</span>
						</form>
					</div>
				</div>
			</li>
		</ul>