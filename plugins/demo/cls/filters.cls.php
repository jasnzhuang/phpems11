<?php

namespace PHPEMS\plugins\demo;
use function \PHPEMS\M;
use function \PHPEMS\P;
class filters
{
    /**
     * @return void
     * 此方法必须存在
     */
    public function register()
    {
        M('plugin')->registerFilter('afterGetArticle',array($this,'afterGetArticle'),10);
    }

    public function afterGetArticle($content)
    {
        $content['contentview'] = $content['contentview'] + 300;
        return $content;
    }
}