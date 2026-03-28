<h2 class="title">功能导航</h2>
<ul class="list-unstyled list-txt">
    {x2;tree:$menus,menu,mid}
    {x2;if:in_array($data['currentbasic']['basicexam']['model'],v:menu['basictype'])}
    <li  class="border{x2;if:$method == v:menu['method']} active{x2;endif}">
        <a href="{x2;v:menu['url']}">
            <span class="{x2;v:menu['icon']}"></span> {x2;v:menu['title']}
        </a>
    </li>
    {x2;endif}
    {x2;endtree}
</ul>